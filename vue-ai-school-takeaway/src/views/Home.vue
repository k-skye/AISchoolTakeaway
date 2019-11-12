<template>
  <div class="home">
    <div class="header">
      <div class="address_map">
        <span>{{ wetherMessage }}</span>
      </div>
      <!-- @click="$router.push('/search')" -->
      <div class="search_wrap">
        <van-search
          v-model="value"
          placeholder="搜索一下"
          shape="round"
          @search="onSearch"
          @input="onInput"
          @clear="onClear"
          @blur="onBlur"
          @focus="onFocus"
        />
        <div
          v-show="showResult"
          class="serachlist"
        >
          <div class="searchcells">
            <van-cell
              v-for="(result,index) in serachResult"
              :key="index"
              :title="result.name"
              is-link
              @click="$router.push({name: 'shop',params: {restID: result.id}})"
            />
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <!-- 轮播 -->
      <div class="swipe">
        <van-swipe
          :autoplay="3000"
          indicator-color="white"
          @change="swipeOnChange"
        >
          <van-swipe-item
            v-for="(img,index) in swipeImgs"
            :key="index"
            @click="swipeClick"
          >
            <img
              :src="img"
              alt
            >
          </van-swipe-item>
        </van-swipe>
      </div>
      <!-- 分类 -->
      <div class="entries">
        <div
          v-for="(item,index) in menu"
          :key="index"
          class="foodentry"
          @click="entriesClick(item.roomNum)"
        >
          <div class="img_wrap">
            <img
              :src="item.image"
              alt
            >
          </div>
          <span>{{ item.name }}</span>
        </div>
      </div>
    </div>

    <div class="chooseRest">
      <van-sticky>
        <!-- 导航 -->
        <FilterView
          :filter-data="filterData"
          @update="update"
        />
      </van-sticky>
      <!-- 商家信息 -->
      <van-list
        v-model="loading"
        :finished="allLoaded"
        finished-text="没有更多了"
        @load="loadMore"
      >
        <div class="shoplist">
          <IndexShop
            v-for="(item,index) in restaurants"
            :key="index"
            :restaurant="item"
          />
        </div>
      </van-list>
    </div>
  </div>
</template>

<script>
import FilterView from "../components/FilterView";
import IndexShop from "../components/IndexShop";
import { Dialog } from "vant";
export default {
  name: "Home",
  components: {
    FilterView,
    IndexShop
  },
  data() {
    return {
      homeData: null,
      currentSwipe: 0,
      serachResult: [],
      value: null,
      showResult: false,
      swipeImgs: [],
      wetherMessage: "今天吃点什么呢？",
      menu: [
        {
          name: "一饭",
          image: "http://tatestapi.pykky.com/homeImg/icons8-1-50.png",
          roomNum: 1
        },
        {
          name: "二饭",
          image: "http://tatestapi.pykky.com/homeImg/icons8-2-50.png",
          roomNum: 2
        },
        {
          name: "门口/其他",
          image: "http://tatestapi.pykky.com/homeImg/icons8-8-50.png",
          roomNum: 5
        },
        {
          name: "三饭",
          image: "http://tatestapi.pykky.com/homeImg/icons8-3-50.png",
          roomNum: 3
        },
        {
          name: "四饭",
          image: "http://tatestapi.pykky.com/homeImg/icons8-4-50.png",
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
    swipeClick() {
      window.location.href = this.homeData.ImgUrl[this.currentSwipe];
    },
    swipeOnChange(index) {
      this.currentSwipe = index;
    },
    onFocus() {
      this.showResult = true;
    },
    onBlur() {
      if (!this.value) {
        this.showResult = false;
      }
    },
    onClear() {
      this.showResult = false;
    },
    onInput() {
      this.$axios("http://tatestapi.pykky.com/?s=HomeData.GetSearchByRule", {
        params: {
          text: this.value
        }
      }).then(res => {
        if (JSON.stringify(res.data.data) != "{}") {
          this.serachResult = res.data.data;
        }
      });
      if (!this.value) {
        this.showResult = false;
      }
    },
    onSearch() {
      this.$router.push({
        name: "search",
        params: { restaurants: this.serachResult, title: this.value }
      });
    },
    entriesClick(roomNum) {
      if (roomNum == 5) {
        Dialog({
          message:
            "这里可以代买校门口和其他任何店里你想买的东西，还在内测噢～预计两周后上线！尽情期待～"
        });
      } else {
        this.$router.push({ name: "filter", params: { roomNum: roomNum } });
      }
    },
    getData() {
      this.$axios("http://tatestapi.pykky.com/?s=HomeData.GetHeadAdImg").then(
        res => {
          this.swipeImgs = JSON.parse(res.data.data).headAdImg;
          this.homeData = JSON.parse(res.data.data);
        }
      ); //加载广告
      /* this.$axios("http://wthrcdn.etouch.cn/weather_mini?city=花都").then(
        res => {
          const today = res.data.data.forecast[0];
          const okStr = "今天" + today.type + "天，吃点什么呢？";
          this.wetherMessage = okStr;
        }
      ); //加载天气 */
      this.firstLoadData(); //加载商家
    },
    firstLoadData() {
      this.offset = 1;
      this.allLoaded = false;
      // 拉取商家信息
      this.$axios("http://tatestapi.pykky.com/?s=Restaurant.GetRestsByRule", {
        params: {
          page: this.offset,
          condition: this.condition
        }
      }).then(res => {
        this.restaurants = res.data.data;
      });
    },
    loadMore() {
      // 异步更新数据
      setTimeout(() => {
        if (!this.allLoaded) {
          this.offset++;
          // 拉取商家信息
          this.$axios(
            "http://tatestapi.pykky.com/?s=Restaurant.GetRestsByRule",
            {
              params: {
                page: this.offset,
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
    }
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
      margin-top: 2px;
      .serachlist {
        overflow: auto;
        height: 50%;
        width: 100%;
        box-sizing: border-box;
        padding: 0 20px;
        position: fixed;
        z-index: 999;
        .searchcells {
          background-color: white;
          border-radius: 0 0 15px 15px;
          .van-cell {
            border-radius: 0 0 15px 15px;
          }
        }
      }
    }
  }
  .container {
    background-color: white;
    .swipe {
      //height: 100px;
      border-radius: 15px;
      padding: 0 10px;
      img {
        width: 100%;
        //height: 100px;
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
            height: 75%;
          }
        }
      }
    }
  }
}
</style>
