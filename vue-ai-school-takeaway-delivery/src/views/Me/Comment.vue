<template>
  <div class="comment">
    <van-popup v-model="showReply" closeable :style="{ height: '20%',width: '80%' }">
      <div class="head">回复评论</div>
      <div class="contain">
        <van-cell-group title=" ">
          <van-field
            v-model="message"
            type="textarea"
            placeholder="请输入内容"
            rows="1"
            autosize
          >
            <van-button slot="button" size="small" type="primary" @click="onReplyToButtonClick">回复</van-button>
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
        <van-collapse v-model="activeNames">
          <van-collapse-item name="1" :icon="status == 0 ? '' : 'shop-o'">
            <div slot="title" class="title">
              <van-tag v-if="status == 0 ? true : false" type="primary">待回复</van-tag>
              <span v-if="status==0 ? true : false">&nbsp&nbsp第四饭堂</span>
              <span v-else>第四饭堂</span>
              <van-icon name="arrow" class="icon" />C15
              <div class="end">
                <van-icon name="clock-o" class="icon" />08-25 12:24
              </div>
            </div>
            <div class="foods">
              <ul>
                <li>
                  <div class="food">
                    <div class="name">1.招牌手撕鸡</div>
                    <div class="price">¥14.00</div>
                  </div>
                </li>
                <li>
                  <div class="food">
                    <div class="name">2.湿炒河粉</div>
                    <div class="price">¥18.00</div>
                  </div>
                </li>
              </ul>
            </div>
            <van-divider />
            <div class="bottom">
              <div class="userComment">
                <div class="title">客户评价：</div>
                <div class="content">我觉得很不错</div>
              </div>
              <van-divider />
              <div class="deliver">
                <div class="deliveComment">
                  <div class="title">您的回复：</div>
                  <div class="content">未回复</div>
                </div>
                <div class="replayButton" v-if="status == 0 ? true : false">
                  <van-button type="info" @click="onReplyButtonClick">回复评价</van-button>
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
export default {
  name: "comment",
  data() {
    return {
      loading: false,
      finished: false,
      list: [],
      activeNames: [],
      status: 0,
      showReply: false,
      message: ""
    };
  },
  methods: {
    onLoad() {
      // 异步更新数据
      setTimeout(() => {
        for (let i = 0; i < 10; i++) {
          this.list.push(this.list.length + 1);
        }
        // 加载状态结束
        this.loading = false;

        // 数据全部加载完成
        if (this.list.length >= 40) {
          this.finished = true;
        }
      }, 500);
    },
    onReplyButtonClick() {
      this.showReply = true;
    },
    onReplyToButtonClick(){
        this.showReply = false;
        Toast.success("成功");
        this.status = 1;
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
  .head{
      display: flex;
      justify-content: center;
      margin-top: 18px;
  }
  .title {
    display: flex;
    align-items: center;
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