<template>
  <div class="home">
    <div class="header">
      <div class="address_map" @click="$router.push({name: 'address'})">
        <i class="fa fa-map-marker"></i>
        <span>{{address}}</span>
        <i class="fa fa-sort-desc"></i>
      </div>
    </div>
    <div class="search_wrap" @click="$router.push('/search')">
      <div class="shop_search">
        <i class="fa fa-search"></i>
        搜索商家 商家名称
      </div>
    </div>
    <div id="container">
      <!-- 轮播 -->
      <mt-swipe :auto="4000" class="swiper">
        <mt-swipe-item v-for="(img,index) in swipeImgs" :key="index">
          <img :src="img" alt>
        </mt-swipe-item>
      </mt-swipe>
      <!-- 分类 -->
      <div class="entries">
          <div class="foodentry" v-for="(item,index) in menu" :key="index">
            <div class="img_wrap">
              <img :src="item.image" alt>
            </div>
            <span>{{item.name}}</span>
          </div>
      </div>
    </div>
    <!-- 推荐商家 -->
    <div class="shoplist-title">推荐商家</div>

    <!-- 导航 -->
    <FilterView :filterData="filterData" @update="update"/>

    <!-- 商家信息 -->
    <mt-loadmore
      :bottom-method="loadMore"
      :bottom-all-loaded="allLoaded"
      :auto-fill="false"
      :bottomPullText="bottomPullText"
      ref="loadmore"
    >
      <div class="shoplist">
        <IndexShop v-for="(item,index) in restaurants" :key="index" :restaurant="item"/>
      </div>
    </mt-loadmore>
  </div>
</template>

<script>
import { Swipe, SwipeItem, Loadmore } from "mint-ui";
import FilterView from "../components/FilterView";
import IndexShop from "../components/IndexShop";
export default {
  name: "home",
  data() {
    return {
      swipeImgs: [],
      menu: [{name: "美食", image: "https://fuss10.elemecdn.com/7/d8/a867c870b22bc74c87c348b75528djpeg.jpeg"},
      {name: "美食", image: "https://fuss10.elemecdn.com/7/d8/a867c870b22bc74c87c348b75528djpeg.jpeg"}],
      filterData: [{name: "综合排序", condition: "normal"},
      {name: "销量最高", condition: "sale"},
      {name: "好评优先", condition: "stars"}],
      offset: 1,
      size: 5,
      restaurants: [], // 存放所有商家容器
      allLoaded: false,
      bottomPullText: "",
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
      this.$axios("/api/?s=HomeData.GetHeadAdImg").then(res => {
        this.swipeImgs = JSON.parse(res.data.data).headAdImg;
      });//加载广告
      this.firstLoadData();//加载商家
    },
    firstLoadData() {
      this.offset = 1;
      this.allLoaded = false;
      // 拉取商家信息
      this.$axios
        .post("/api/?s=Restaurant.GetRestsByRule",{
          offset: this.offset,
          limit: this.size,
          condition: this.condition
        })
        .then(res => {
          this.restaurants = res.data.data;
        });
    },
    loadMore() {
      if (!this.allLoaded) {
         this.offset+=5;
        // 拉取商家信息
        this.$axios
          .post("/api/?s=Restaurant.GetRestsByRule",{
          offset: this.offset,
          limit: this.size,
          condition: this.condition
          })
          .then(res => {
            //  加载完之后重新渲染
            this.$refs.loadmore.onBottomLoaded();
            if (res.data.length > 0) {
              res.data.forEach(item => {
                this.restaurants.push(item);
              });
              if (res.data < this.size) {
                this.allLoaded = true;
                this.bottomPullText = "没有更多了哦";
              }
            } else {
              // 数据为空
              this.allLoaded = true;
              this.bottomPullText = "没有更多了哦";
            }
          });
      }
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

<style scoped>
.home {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
}
.header,
.search_wrap {
  background-color: #009eef;
  padding: 10px 16px;
}
.header .address_map {
  color: #fff;
  font-weight: bold;
}
.address_map i {
  margin: 0 3px;
  font-size: 18px;
}
.address_map span {
  display: inline-block;
  width: 80%;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}
.search_wrap .shop_search {
  /* margin-top: 10px; */
  background-color: #fff;
  padding: 10px 0;
  border-radius: 4px;
  text-align: center;
  color: #aaa;
}

.search_wrap {
  position: sticky;
  top: 0px;
  z-index: 999;
  box-sizing: border-box;
}
.swiper {
  height: 100px;
}
.swiper img {
  width: 100%;
  height: 100px;
}

.entries {
  background: #fff;
  height: 47.2vw;
  text-align: center;
  overflow: hidden;
}
.foodentry {
  width: 20%;
  float: left;
  position: relative;
  margin-top: 2.933333vw;
}
.foodentry .img_wrap {
  position: relative;
  display: inline-block;
  width: 12vw;
  height: 12vw;
}
.img_wrap img {
  width: 100%;
  height: 100%;
}
.foodentry span {
  display: block;
  color: #666;
  font-size: 0.32rem;
}
/* 推荐商家 */
.shoplist-title {
  display: flex;
  align-items: flex;
  justify-content: center;
  height: 9.6vw;
  line-height: 9.6vw;
  font-size: 16px;
  color: #333;
  background: #fff;
}
.shoplist-title:after,
.shoplist-title:before {
  display: block;
  content: "一";
  width: 5.333333vw;
  height: 0.266667vw;
  color: #999;
}
.shoplist-title:before {
  margin-right: 3.466667vw;
}
.shoplist-title:after {
  margin-left: 3.466667vw;
}

.mint-loadmore {
  height: calc(100% - 95px);
  overflow: auto;
}
</style>
