<template>
  <div class="comment">
    <van-popup v-model="showReply" closeable :style="{ height: '30%',width: '80%' }">
      <div class="head">回复评论</div>
      <div class="contain">
        <van-cell-group title=" ">
          <van-field v-model="message" type="textarea" placeholder="请输入内容" rows="1" autosize>
            <van-button :disabled="hasText" slot="button" size="small" type="primary" @click="onReplyToButtonClick">回复</van-button>
          </van-field>
        </van-cell-group>
      </div>
    </van-popup>
    <div class="header">
      <van-nav-bar title="评价" left-arrow @click-left="$router.push('me')" />
    </div>
    <div class="contain">
      <van-list v-model="loading" :finished="finished" finished-text="没有更多了" @load="onLoad">
        <!-- <van-cell v-for="item in list" :key="item" :title="item" /> -->
        <van-collapse v-model="activeNames" v-for="(orderDelive,index) in orders" :key="index">
          <van-collapse-item :name="index" :icon="orderDelive.hasComment == 0 ? '' : 'shop-o'">
            <div slot="title" class="title">
              <van-tag v-if="orderDelive.hasComment == 0 ? true : false" type="primary">待回复</van-tag>
              <span
                v-if="orderDelive.hasComment==0 ? true : false"
              >第{{orderDelive.order.restNum}}饭堂</span>
              <span v-else>第{{orderDelive.order.restNum}}饭堂</span>
              <van-icon name="arrow" class="icon" />
              {{orderDelive.dormitory}}
              <div class="end">
                <van-icon name="clock-o" class="icon" />
                {{orderDelive.delivedTime}}
              </div>
            </div>
            <div class="foods">
              <ul>
                <li v-for="(food,indexFood) in orderDelive.order.foodsArr" :key="indexFood">
                  <div class="food">
                    <div class="name">{{indexFood+1}}.{{food.name}}</div>
                    <div class="price">¥{{parseFloat(food.price).toFixed(2)}}</div>
                  </div>
                </li>
              </ul>
            </div>
            <van-divider />
            <div class="bottom">
              <div class="userComment">
                <div class="title">客户评价：</div>
                <div class="content">{{orderDelive.comment.content}}</div>
              </div>
              <van-divider />
              <div class="deliver">
                <div class="deliveComment">
                  <div class="title">您的回复：</div>
                  <div
                    class="content"
                  >{{orderDelive.comment.deliverReply == "未回复"?'未回复':orderDelive.comment.deliverReply}}</div>
                </div>
                <div class="replayButton" v-if="orderDelive.hasComment == 0 ? true : false">
                  <van-button type="info" @click="onReplyButtonClick(index,orderDelive.comment.id,orderDelive.id)">回复评价</van-button>
                </div>
              </div>
            </div>
          </van-collapse-item>
        </van-collapse>
      </van-list>
    </div>
  </div>
</template>

<script>
import { Toast } from "vant";
import { Dialog } from "vant";
export default {
  name: "comment",
  data() {
    return {
      orderlist: [], //存放当前订单容器
      orders: [], //存放所有订单容器
      loading: false,
      finished: false,
      activeNames: [],
      showReply: false,
      message: "",
      deliverID: null,
      offset: 1,
      size: 5,
      indexx: 0,
      commentID: 0,
      deliveOrderID: 0
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.deliverID = to.params.deliverID;
      vm.firstLoadData();
    });
  },
  methods: {
    firstLoadData() {
      this.offset = 1;
      this.finished = false;
      // 拉取商家信息
      this.$axios(
        "https://takeawayapi.pykky.com/?s=Deliverorders.GetAllOrderCountCanComment",
        {
          params: {
            deliverID: this.deliverID,
            offset: this.offset,
            limit: this.size
          }
        }
      ).then(res => {
        if (JSON.stringify(res.data.data) == "{}") {
          this.finished = true;
          this.loading = false;
          return;
        }
        this.orderlist = res.data.data;
        this.orders = res.data.data;
        this.handleData();
      });
    },
    handleData() {
      //对商品数据处理
      var i = 0;
      this.orderlist.forEach(orders => {
        var showfood = "";
        var OrderFoods = JSON.parse(orders.order.foods);
        var foodsArr = new Array();
        OrderFoods.forEach(id => {
          orders.food.forEach(food => {
            if (food.id == id) {
              foodsArr.push(food); //把每行food的全部数据对象都放入数组
            }
          });
        });
        this.orders[i].order.foodsArr = foodsArr;
        this.orders[i].order.foodsCount = foodsArr.length; //食物数量
        i++;
      });
    },
    onLoad() {
      // 异步更新数据
      setTimeout(() => {
        if (!this.finished) {
          this.offset += (parseInt(this.orderlist[this.orderlist.length-1].id));
          // 拉取商家信息
          this.$axios(
            "https://takeawayapi.pykky.com/?s=Deliverorders.GetAllOrderCountCanComment",
            {
              params: {
                deliverID: this.deliverID,
                offset: this.offset,
                limit: this.size
              }
            }
          ).then(res => {
            if (JSON.stringify(res.data.data) != "{}") {
              res.data.data.forEach(element => {
                element.images = JSON.parse(element.images);
                this.orderlist.push(element);
              });
              this.handleData();
              this.loading = false;
              if (res.data < this.size) {
                this.finished = true;
                this.loading = false;
              }
            } else {
              // 数据为空
              this.finished = true;
              this.loading = false;
            }
          });
        }
      }, 500);
    },
    onReplyButtonClick(index,comID,deorderID) {
      this.showReply = true;
      this.indexx = index;
      this.commentID = comID;
      this.deliveOrderID = deorderID;
    },
    onReplyToButtonClick() {
      Dialog.confirm({
        title: "确定回复吗？"
      })
        .then(() => {
          this.$axios(
            "https://takeawayapi.pykky.com/?s=Comment.CommentDeliveReply",
            {
              params: {
                ID: this.commentID,
                text: this.message,
                deliveOrderID: this.deliveOrderID
              }
            }
          ).then(res => {
            if (res.data.data == "ok") {
              this.$set(this.orders[this.indexx], "hasComment", 1); //让开头的图标变化
              this.$set(
                this.orders[this.indexx].comment,
                "deliverReply",
                this.message
              ); //让文字变化
              this.activeNames.forEach((item, index) => {
                if (item == this.indexx) {
                  this.activeNames.splice(index, 1);
                }
              });
              this.showReply = false;
              Toast.success("成功");
            } else {
              Toast.fail("失败！" + res.data.msg);
            }
          });
        })
        .catch(() => {
          // on cancel
        });
    }
  },
  computed:{
    hasText(){
      if (this.message == "") {
        return true;
      }else{
        return false;
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.comment {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  .head {
    display: flex;
    justify-content: center;
    margin-top: 18px;
  }
  .title {
    display: flex;
    align-items: center;
    .van-tag{
      margin-right: 5px;
    }
    .icon {
      padding: 0 5px;
    }
    .end {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      flex-grow: 1;
    }
  }
  .foods {
    padding: 0 10px;
    .food {
      display: flex;
      justify-content: space-between;
    }
  }
  .bottom {
    .userComment {
      display: flex;
      .title {
        color: black;
      }
    }
    .deliver {
      display: flex;
      justify-content: space-between;
      .deliveComment {
        display: flex;
        align-items: center;
        .title {
          color: black;
        }
      }
    }
  }
}
</style>