<template>
  <div class="allcomment">
    <div class="header">
      <van-nav-bar title="评价" left-arrow @click-left="$router.push('me')" />
    </div>
    <div class="contain">
      <van-list v-model="loading" :finished="finished" finished-text="没有更多了" @load="onLoad">
        <div class="order-card-body" v-for="(comment,index) in commentlist" :key="index">
          <div class="order-card-wrap">
            <img
              :src="'https://takeawayschool.oss-cn-shenzhen.aliyuncs.com/restImgs/'+comment.restLogo"
              alt
            />
            <div class="order-card-content">
              <div
                class="order-card-head"
                @click="$router.push({name: 'shop',params: {restID: comment.restID}})"
              >
                <div class="title">
                  <a>
                    <span>{{comment.restName}}</span>
                    <van-icon name="arrow" />
                  </a>
                  <p>{{comment.deliverReply=='未回复'?'伙伴未回复':'伙伴已回复'}}</p>
                </div>
                <p class="date-time">{{comment.time}}</p>
              </div>
              <div class="order-card-detail">
                <p class="price">{{comment.content}}</p>
              </div>
              <ul class="comment-imgs">
                <li v-for="(img,i) in comment.images" :key="i">
                  <img :src="'https://takeawayschool.oss-cn-shenzhen.aliyuncs.com/'+img" alt />
                </li>
              </ul>
              <div class="reply" v-show="comment.deliverReply!='未回复'">伙伴：{{comment.deliverReply}}</div>
            </div>
          </div>
        </div>
      </van-list>
    </div>
  </div>
</template>

<script>
export default {
  name: "allcomment",
  data() {
    return {
      commentlist: [], //存放容器
      loading: false,
      finished: false,
      userID: null,
      offset: 1,
      size: 5,
      status: ""
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.userID = to.params.userID;
      vm.firstLoadData();
    });
  },
  methods: {
    firstLoadData() {
      this.offset = 1;
      this.finished = false;
      // 拉取商家信息
      this.$axios("https://takeawayapi.pykky.com/?s=Comment.GetOnesComment", {
        params: {
          userID: this.userID,
          offset: this.offset,
          limit: this.size
        }
      }).then(res => {
        if (JSON.stringify(res.data.data) == "{}") {
          this.finished = true;
          this.loading = false;
          return;
        }
        res.data.data.forEach(element => {
          element.images = JSON.parse(element.images);
        });
        this.commentlist = res.data.data;
      });
    },
    onLoad() {
      // 异步更新数据
      setTimeout(() => {
        if (!this.finished) {
          this.offset += (parseInt(this.commentlist[this.commentlist.length-1].id));
          // 拉取商家信息
          this.$axios(
            "https://takeawayapi.pykky.com/?s=Comment.GetOnesComment",
            {
              params: {
                userID: this.userID,
                offset: this.offset,
                limit: this.size
              }
            }
          ).then(res => {
            if (JSON.stringify(res.data.data) != "{}") {
              res.data.data.forEach(element => {
                element.images = JSON.parse(element.images);
                this.commentlist.push(element);
              });
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
    }
  }
};
</script>

<style lang="scss" scoped>
.allcomment {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  .head {
    display: flex;
    justify-content: center;
    margin-top: 18px;
  }
  .order-card-body {
    margin-top: 2.666667vw;
    background-color: #fff;
    padding: 3.733333vw 0 0 4vw;
  }
  .order-card-wrap {
    display: flex;
  }
  .order-card-wrap > img {
    height: 8.533333vw;
    border-radius: 0.533333vw;
    border: 1px solid #eee;
    width: 8.533333vw;
    margin-right: 2.666667vw;
  }
  .order-card-content {
    flex: 1;
  }
  .order-card-head {
    border-bottom: 1px solid #eee;
    padding-right: 3.466667vw;
    padding-bottom: 2.666667vw;
  }
  .order-card-head .title {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .order-card-head .title > a {
    font-size: 1rem;
    line-height: 1.5em;
    color: #333;
    text-decoration: none;
    display: flex;
    align-items: center;
  }
  .order-card-head .title > a > span {
    display: inline-block;
    max-width: 10em;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .order-card-head .title > p {
    font-size: 0.8rem;
    text-align: right;
    color: #333;
    max-width: 14em;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .date-time {
    font-size: 0.6rem;
    color: #999;
  }
  .order-card-detail {
    justify-content: space-between;
    padding: 4vw 3.466667vw 4vw 0;
    font-size: 0.8rem;
    color: #323233;
  }
  .order-card-detail .detail {
    color: #323233;
    display: flex;
    align-items: center;
  }
  .order-card-detail .price {
    flex-basis: 16vw;
    color: #323233;
  }
  .reply {
    display: flex;
    justify-content: space-between;
    padding: 4vw 3.466667vw 4vw 5vw;
    font-size: 0.8rem;
    margin-left: 5vw;
    margin-right: 8vw;
    margin-bottom: 10px;
    color: #323233;
    background-color: #f8f8f8;
  }
  .order-card-bottom {
    display: flex;
    border-top: 1px solid #eee;
    padding: 2.666667vw 4.266667vw;
    justify-content: flex-end;
  }
  .comment-imgs {
    margin-top: 1.066667vw;
    margin-bottom: 3.2vw;
  }
  .comment-imgs > li {
    display: inline-block;
    margin: 0 0.533333vw;
  }
  .comment-imgs > li img {
    width: 20vw;
    height: 20vw;
  }
}
</style>