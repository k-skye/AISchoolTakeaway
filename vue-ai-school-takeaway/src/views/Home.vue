<template>
  <div class="home">
    <div class="header">
      <div class="address_map">
        <span>下午好！</span>
      </div>
      <div class="search_wrap" @click="$router.push('/search')">
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
        <div class="foodentry" v-for="(item,index) in menu" :key="index">
          <div class="img_wrap">
            <img :src="item.image" alt />
          </div>
          <span>{{item.name}}</span>
        </div>
      </div>
    </div>

    <van-sticky>
      <div class="chooseRest">
        <!-- 导航 -->
        <FilterView :filterData="filterData" @update="update" />

        <!-- 商家信息 -->
        <van-list v-model="loading" :finished="allLoaded" finished-text="没有更多了" @load="loadMore">
            <div class="shoplist">
              <IndexShop v-for="(item,index) in restaurants" :key="index" :restaurant="item" />
            </div>
        </van-list>
      </div>
    </van-sticky>
  </div>
</template>

<script>
import FilterView from "../components/FilterView";
import IndexShop from "../components/IndexShop";
export default {
  name: "home",
  data() {
    return {
      swipeImgs: [],
      menu: [
        {
          name: "美食",
          image:
            "https://fuss10.elemecdn.com/7/d8/a867c870b22bc74c87c348b75528djpeg.jpeg"
        },
        {
          name: "美食",
          image:
            "https://fuss10.elemecdn.com/7/d8/a867c870b22bc74c87c348b75528djpeg.jpeg"
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
      this.firstLoadData(); //加载商家
    },
    firstLoadData() {
      this.offset = 1;
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
          this.offset += 5;
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
            if (res.data.length > 0) {
              res.data.forEach(item => {
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
    background-color: #009eef;
    .address_map {
      color: #fff;
      font-weight: bold;
      padding: 0 16px;
      padding-top: 10px;
    }
    .search_wrap {
      padding: 10px 16px;
      .shop_search {
        /* margin-top: 10px; */
        background-color: #fff;
        padding: 10px 0;
        border-radius: 4px;
        text-align: center;
        color: #aaa;
      }
    }
  }
  .container {
    .swipe {
      height: 100px;
      img {
        width: 100%;
        height: 100px;
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
          font-size: 0.32rem;
        }
        .img_wrap {
          position: relative;
          display: inline-block;
          width: 12vw;
          height: 12vw;
          img {
            width: 100%;
            height: 100%;
          }
        }
      }
    }
  }
}
</style>
