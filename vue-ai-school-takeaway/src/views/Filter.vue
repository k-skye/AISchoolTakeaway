<template>
  <div class="filter">
    <div class="header">
      <van-nav-bar
        :title="title"
        left-arrow
        @click-left="$router.go(-1)"
      />
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
export default {
  name: "Filter",
  components: {
    FilterView,
    IndexShop
  },
  data() {
    return {
      title: "第一饭堂",
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
      condition: "",
      roomNum: null
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      const room = to.params.roomNum;
      switch(room){
        case 1:
          vm.title = '第一饭堂'
          break;
        case 2:
          vm.title = '第二饭堂'
          break;
        case 3:
          vm.title = '第三饭堂'
          break;
        case 4:
          vm.title = '第四饭堂'
          break;
        default:
          vm.title = '校门口/其他'
          break;
      }
      vm.roomNum = room;
      vm.getData();
      });
  },
  methods: {
    getData() {
      this.firstLoadData(); //加载商家
    },
    firstLoadData() {
      this.offset = 1;
      this.allLoaded = false;
      // 拉取商家信息
      this.$axios(
        "http://123.207.230.132:1203/?s=Restaurant.GetRestsByRuleWithRoomNum",
        {
          params: {
            page: this.offset,
            condition: this.condition,
            roomNum: this.roomNum
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
          this.offset++;
          // 拉取商家信息
          this.$axios(
            "http://123.207.230.132:1203/?s=Restaurant.GetRestsByRuleWithRoomNum",
            {
              params: {
                page: this.offset,
                condition: this.condition,
                roomNum: this.roomNum
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
  }
};
</script>

<style lang="scss" scoped>
.filter {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
}
</style>
