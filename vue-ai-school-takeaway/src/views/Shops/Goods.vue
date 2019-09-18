<template>
  <div class="goods" v-if="foodInfo">
    <!-- 商品分类 -->
    <div class="menuview">
      <!-- 左侧分类列表 -->
      <div class="menu-wrapper" ref="menuScroll">
        <ul>
          <li
            :class="{'current':currentIndex === index}"
            @click="selectMenu(index)"
            v-for="(item,index) in foodInfo"
            :key="index"
          >
            <img v-if="item.icon_url" :src="item.icon_url" alt />
            <span>{{item.cate}}</span>
          </li>
        </ul>
      </div>

      <!-- 右侧商品内容 -->
      <div class="foods-wrapper" ref="foodScroll">
        <ul>
          <li class="food-list-hook" v-for="(item,index) in foodInfo" :key="index">
            <!-- 内容上 -->
            <div class="category-title">
              <strong>{{item.cate}}</strong>
              <span>不知道是什么的描述</span>
            </div>
            <!-- 内容下 -->
            <div
              @click="handleFood(food)"
              class="fooddetails"
              v-for="(food,i) in item.food"
              :key="i"
            >
              <!-- 左 -->
              <img :src="food.logo" alt />
              <!-- 右 -->
              <section class="fooddetails-info">
                <h4>{{food.name}}</h4>
                <p class="fooddetails-des">不知道是什么的物品描述</p>
                <p class="fooddetails-sales">月售{{food.salesNum}}份 好评率123</p>
                <div class="fooddetails-price">
                  <span class="price">¥{{food.price}}</span>
                  <CartControll :food="food" />
                </div>
              </section>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- 购物车 -->
    <ShopCart :foodInfo="foodInfo" />

    <!-- 商品详情 -->
    <Food :food="selectedFood" :isShow="showFood" @close="showFood=false" />
  </div>
</template>

<script>
import CartControll from "../../components/Shops/CartControll";
import BScroll from "better-scroll";
import ShopCart from "./ShopCart";
import Food from "./Food";
export default {
  name: "Goods",
  data() {
    return {
      restID: "1",
      foodInfo: [],
      menuScroll: {}, // 左侧滚动对象
      foodScroll: {}, // 右侧滚动对象
      scrollY: 0, // 右侧菜单当前滚动到的y值
      listHeight: [], // 12个区列表高度
      selectedFood: null,
      showFood: false
    };
  },
  created() {
    this.getData();
  },
  computed: {
    // 根据右侧滚动的位置, 确定对应的索引下标
    currentIndex() {
      for (let i = 0; i < this.listHeight.length; i++) {
        let height1 = this.listHeight[i];
        let height2 = this.listHeight[i + 1];

        // 判断是否在两个高度之间
        if (this.scrollY >= height1 && this.scrollY < height2) {
          return i;
        }
      }

      return 0;
    }
  },
  methods: {
    getData() {
      this.$axios("/api/?s=Food.GetFoods", {
        params: {
          restID: this.restID
        }
      }).then(res => {
        res.data.data.forEach(cate => {
          cate.count = 0;
        });

        res.data.data.forEach(cate => {
          cate.food.forEach(food => {
            food.count = 0;
          });
        });

        this.foodInfo = res.data.data;
        this.$nextTick(() => {
          // DOM已经更新
          this.initScroll();
          // 计算12个区的高度
          this.calculateHeight();
        });
      });
    },
    initScroll() {
      this.menuScroll = new BScroll(this.$refs.menuScroll, {
        click: true
      });

      this.foodScroll = new BScroll(this.$refs.foodScroll, {
        probeType: 3,
        click: true
      });

      this.foodScroll.on("scroll", pos => {
        // console.log(pos.y);
        this.scrollY = Math.abs(Math.round(pos.y));
      });
    },
    selectMenu(index) {
      // console.log(index);
      let foodlist = this.$refs.foodScroll.getElementsByClassName(
        "food-list-hook"
      );

      let el = foodlist[index];
      // console.log(el);
      // 滚动到对应元素的位置
      this.foodScroll.scrollToElement(el, 250);
    },
    calculateHeight() {
      let foodlist = this.$refs.foodScroll.getElementsByClassName(
        "food-list-hook"
      );
      // 每个区的高度添加到数组中
      let height = 0;
      this.listHeight.push(height);

      for (let i = 0; i < foodlist.length - 1; i++) {
        let item = foodlist[i];
        // 累加
        height += item.clientHeight;
        this.listHeight.push(height);
      }
      // console.log(this.listHeight);
    },
    handleFood(food) {
      console.log(food);
      this.selectedFood = food;
      this.showFood = true;
    }
  },
  components: {
    CartControll,
    ShopCart,
    Food
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.restID = to.params.restID;
    });
  }
};
</script>

<style scoped>
.goods {
  position: relative;
  height: calc(100% - 10.666667vw);
}

::-webkit-scrollbar {
  width: 0 !important;
}

.menuview {
  box-sizing: border-box;
  height: 100%;
  padding-bottom: 10.8vw;
  background-color: #fff;
  display: flex;
}
.menu-wrapper {
  overflow-y: hidden;
  /* height: 100%; */
  height: calc(100% - 12.8vw);
  background-color: #f8f8f8;
  padding-bottom: 10.666667vw;
  width: 20.533333vw;
}
.menu-wrapper li {
  padding: 4.666667vw 2vw;
  font-size: 0.6rem;
  color: #666;
  line-height: 1.2;
}
.menu-wrapper li img {
  max-width: 100%;
  width: 3.466667vw;
  height: 3.466667vw;
  vertical-align: top;
  margin-right: 0.8vw;
}

.foods-wrapper {
  overflow-y: hidden;
  /* height: 100%; */
  height: calc(100% - 12.8vw);
  width: 79.466667vw;
  padding-bottom: 10.666667vw;
}
.category-title {
  margin-left: 2.666667vw;
  padding: 2vw 8vw 2vw 0;
  display: flex;
  align-items: center;
  overflow: hidden;
}
.category-title strong {
  margin-right: 1.333333vw;
  font-weight: 700;
  font-size: 0.8rem;
  color: #666;
  flex: none;
}
.category-title span {
  flex: 1;
  color: #999;
  font-size: 0.6rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.fooddetails {
  min-height: 30.666667vw;
  padding: 2.666667vw 0 2.666667vw 2.666667vw;
  margin-bottom: 0.133333vw;
  display: flex;
}
.fooddetails img {
  width: 25.333333vw;
  height: 25.333333vw;
  flex: none;
  margin-right: 2.666667vw;
  border-radius: 0.533333vw;
}
.fooddetails-info {
  flex: 1;
  padding-bottom: 6.666667vw;
  padding-right: 4vw;
}
.fooddetails-info h4 {
  padding-right: 4vw;
  font-weight: 700;
  overflow: hidden;
  font-size: 1rem;
  white-space: nowrap;
  width: 40vw;
  text-overflow: ellipsis;
  color: #333;
}
.fooddetails-des {
  margin: 1.333333vw 0;
  font-size: 0.6rem;
  color: #999;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  width: 42.666667vw;
}
.fooddetails-sales {
  margin: 1.733333vw 0 !important;
  color: #999;
  font-size: 0.6rem;
  line-height: 1;
  min-height: 1em;
}
.fooddetails-price {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 3.733333vw;
}
.fooddetails-price .price {
  font-size: 1rem;
  line-height: 4.266667vw;
  color: #ff5339;
  padding-bottom: 0.933333vw;
  display: flex;
  align-items: baseline;
}

.menu-wrapper .current {
  background-color: #fff !important;
  color: #333 !important;
}
</style>
