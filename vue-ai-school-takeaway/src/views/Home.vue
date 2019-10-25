<template>
  <div class="home">
    <div class="header">
      <div class="address_map">
        <span>{{wetherMessage}}</span>
      </div>
      <!-- @click="$router.push('/search')" -->
      <div class="search_wrap" @click="onSearchClick">
        <div class="shop_search">
          <i class="fa fa-search"></i>
          搜索商家 商家名称
        </div>
      </div>
    </div>
    <div class="container">
      <!-- 轮播 -->
      <div class="swipe">
        <van-swipe :autoplay="3000" indicator-color="white">
          <van-swipe-item v-for="(img,index) in swipeImgs" :key="index">
            <img :src="img" alt />
          </van-swipe-item>
        </van-swipe>
      </div>
      <!-- 分类 -->
      <div class="entries">
        <div class="foodentry" v-for="(item,index) in menu" :key="index" @click="$router.push({name: 'filter',params: {roomNum: item.roomNum}})">
          <div class="img_wrap">
            <img :src="item.image" alt />
          </div>
          <span>{{item.name}}</span>
        </div>
      </div>
    </div>

   
      <div class="chooseRest">
         <van-sticky>
        <!-- 导航 -->
        <FilterView :filterData="filterData" @update="update" />
        </van-sticky>
        <!-- 商家信息 -->
        <van-list v-model="loading" :finished="allLoaded" finished-text="没有更多了" @load="loadMore">
            <div class="shoplist">
              <IndexShop v-for="(item,index) in restaurants" :key="index" :restaurant="item" />
            </div>
        </van-list>
      </div>
    
  </div>
</template>

<script>
import FilterView from "../components/FilterView";
import IndexShop from "../components/IndexShop";
import { Toast } from 'vant';
export default {
  name: "home",
  data() {
    return {
      swipeImgs: [],
      wetherMessage:"今天吃点什么呢？",
      menu: [
        {
          name: "一饭",
          image:
            "https://takeawayapi.pykky.com/homeImg/icons8-1-50.png",
          roomNum: 1
        },
        {
          name: "二饭",
          image:
            "https://takeawayapi.pykky.com/homeImg/icons8-2-50.png",
          roomNum: 2
        },
        {
          name: "门口/其他",
          image:
            "https://takeawayapi.pykky.com/homeImg/icons8-8-50.png",
          roomNum: 5
        },
        {
          name: "三饭",
          image:
            "https://takeawayapi.pykky.com/homeImg/icons8-3-50.png",
          roomNum: 3
        },
        {
          name: "四饭",
          image:
            "https://takeawayapi.pykky.com/homeImg/icons8-4-50.png",
          roomNum: 4
        }
      ],
      filterData: [
        { name: "综合排序", condition: "normal" },
        { name: "销量最高", condition: "sale" },
        { name: "好评优先", condition: "stars" }
      ],
      offset: 1,
      size: 5,
      restaurants: [], // 存放所有商家容器
      allLoaded: false,
      loading: false,
      condition: ""
    };
  },
  computed: {
    address() {
      return this.$store.getters.address;
    }
  },
  created() {
    this.getData();
  },
  methods: {
    getData() {
      this.$axios(
        "https://takeawayapi.pykky.com/?s=HomeData.GetHeadAdImg"
      ).then(res => {
        this.swipeImgs = JSON.parse(res.data.data).headAdImg;
      }); //加载广告
      this.$axios(
        "http://wthrcdn.etouch.cn/weather_mini?city=花都"
      ).then(res => {
        const today = res.data.data.forecast[0];
        const okStr = '今天'+today.type+'天，吃点什么呢？';
        this.wetherMessage = okStr;
      }); //加载天气
      this.firstLoadData(); //加载商家
    },
    firstLoadData() {
      this.offset = 7;
      this.allLoaded = false;
      // 拉取商家信息
      this.$axios(
        "https://takeawayapi.pykky.com/?s=Restaurant.GetRestsByRule",
        {
          params: {
            offset: this.offset,
            limit: this.size,
            condition: this.condition
          }
        }
      ).then(res => {
        this.restaurants = res.data.data;
      });
    },
    loadMore() {
      // 异步更新数据
      setTimeout(() => {
        if (!this.allLoaded) {
          if (this.offset == 27) {//处理数据库id中间有一个id按顺序时候漏了，修复后这里可以去掉
            this.offset += 6;
          }
          else{
            this.offset += 5;
          }
          // 拉取商家信息
          this.$axios(
            "https://takeawayapi.pykky.com/?s=Restaurant.GetRestsByRule",
            {
              params: {
                offset: this.offset,
                limit: this.size,
                condition: this.condition
              }
            }
          ).then(res => {
            if (JSON.stringify(res.data.data) != "{}") {
              res.data.data.forEach(item => {
                this.restaurants.push(item);
              });
              this.loading = false;
              if (res.data < this.size) {
                this.allLoaded = true;
                this.loading = false;
              }
            } else {
              // 数据为空
              this.allLoaded = true;
              this.loading = false;
            }
          });
        }
      }, 500);
    },
    update(condition) {
      this.condition = condition.condition;
      this.firstLoadData();
    },
    onSearchClick(){
      Toast('搜索功能完善中...待开放噢～');
    }
  },
  components: {
    FilterView,
    IndexShop
  }
};
</script>

<style lang="scss" scoped>
.home {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  .header {
    //background-color: #009eef;
    background-color: white;
    .address_map {
      color: #323233;
      font-weight: bold;
      padding: 0 16px;
      padding-top: 10px;
    }
    .search_wrap {
      padding: 10px 16px;
      .shop_search {
        /* margin-top: 10px; */
        background-color: #f8f8f8;
        padding: 10px 0;
        border-radius: 4px;
        text-align: center;
        color: #aaa;
      }
    }
  }
  .container {
    background-color: white;
    .swipe {
      height: 100px;
      border-radius: 15px;
      padding: 0 10px; 
      img {
        width: 100%;
        height: 100px;
        border-radius: 15px;
      }
    }
    .entries {
      background: #fff; /* height: 47.2vw; */
      text-align: center;
      overflow: hidden;
      padding-bottom: 10px;
      .foodentry {
        width: 20%;
        float: left;
        position: relative;
        margin-top: 10px;
        span {
          display: block;
          color: #666;
          font-size: 0.7rem;
        }
        .img_wrap {
          position: relative;
          display: inline-block;
          width: 12vw;
          height: 12vw;
          img {
            width: 100%;
            height: 80%;
          }
        }
      }
    }

  }
}
</style>
