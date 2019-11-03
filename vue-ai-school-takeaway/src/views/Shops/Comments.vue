<template>
  <div
    v-if="evaluation"
    class="comment"
  >
    <!-- 评论区 -->
    <div class="shop-info">
      <!-- 标签 
      <ul class="tags">
        <li
          :class="{'unsatisfied':item.unsatisfied}"
          v-for="(item,index) in evaluation.tags"
          :key="index"
        >
          {{item.name}}
          <span v-if="item.count > 0">{{item.count}}</span>
        </li>
      </ul>-->
      <!-- 内容 -->
      <van-list
        v-model="loading"
        :finished="finished"
        finished-text="没有更多了"
        @load="onLoad"
      >
        <ul class="comments-wrap">
          <li
            v-for="(item,index) in evaluation"
            :key="index"
          >
            <div class="comment-user">
              <img
                :src="item.userAvatar"
                alt
              >
            </div>
            <div class="comments-info">
              <div class="comment-name">
                <h4>{{ item.userName }}</h4>
                <small>{{ (item.time).substring(0,(item.time).length-3) }}</small>
              </div>
              <div class="comment-rating">
                <Rating :rating="parseFloat(item.stars)" />
                <span
                  :style="{color: ratingcontent(parseInt(item.stars)).color}"
                >{{ ratingcontent(parseInt(item.stars)).txt }}</span>
              </div>
              <div class="comment-text">
                {{ item.content }}
              </div>
              <div
                v-show="item.reply!='未回复'"
                class="comment-reply"
              >
                {{ '伙伴回复：'+item.reply }}
              </div>
              <ul class="comment-imgs">
                <li
                  v-for="(img,i) in item.images"
                  :key="i"
                >
                  <img
                    :src="'https://takeawayschool.oss-cn-shenzhen.aliyuncs.com/'+img"
                    alt
                  >
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </van-list>
      <div
        v-if="nodata"
        class="nologin"
      >
        <div class="content">
          <van-icon
            name="star-o"
            size="50"
            color="gray"
          />
          <span>本店暂时没有评论噢，快来抢沙发！</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Rating from "../../components/Rating";
export default {
  name: "Comments",
  components: {
    Rating
  },
  data() {
    return {
      restID: "0",
      evaluation: [], //存放容器
      loading: false,
      finished: false,
      offset: 1,
      size: 5,
      nodata: false
    };
  },
  computed: {
    restInfo() {
      return this.$store.getters.restInfo;
    }
  },
  created() {
    this.getData();
  },
  methods: {
    getData() {
      this.restID = this.restInfo.id;
      this.firstLoadData();
    },
    firstLoadData() {
      this.offset = 1;
      this.finished = false;
      // 拉取商家信息
      this.$axios("https://takeawayapi.pykky.com/?s=Comment.GetSomeComment", {
        params: {
          restID: this.restID,
          offset: this.offset,
          limit: this.size
        }
      }).then(res => {
        if (JSON.stringify(res.data.data) == "{}") {
          this.finished = true;
          this.loading = false;
          this.nodata = true;
          return;
        }
        this.evaluation = res.data.data;
      });
    },
    onLoad() {
      // 异步更新数据
      setTimeout(() => {
        if (!this.finished) {
          this.offset += (parseInt(this.evaluation[this.evaluation.length-1].id));
          // 拉取商家信息
          this.$axios(
            "https://takeawayapi.pykky.com/?s=Comment.GetSomeComment",
            {
              params: {
                restID: this.restInfo.id,
                offset: this.offset,
                limit: this.size,
              }
            }
          ).then(res => {
            if (JSON.stringify(res.data.data) != "{}") {
              res.data.data.forEach(element => {
                this.evaluation.push(element);
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
    },
    ratingcontent(rating) {
      const content = [
        { txt: "吐槽", color: "rgb(137,159,188)" },
        { txt: "较差", color: "rgb(137, 159, 188)" },
        { txt: "一般", color: "rgb(251, 154, 11)" },
        { txt: "满意", color: "rgb(251, 154, 11)" },
        { txt: "超赞", color: "rgb(255, 96, 0)" }
      ];
      return content[rating - 1];
    }
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.restID = to.params.restID;
    });
  }
};
</script>

<style scoped lang="scss">
.comment {
  height: calc(100% - 10.666667vw);
  line-height: 1.2;
}
.nologin{
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    .content{
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      .van-icon{
        display: flex;
        justify-content: center;
        width: 100vw;
        padding-bottom: 15px;
      }
      span{
        color: #323233;
      }
    }
  }
.shop-info {
  background-color: #fff;
  padding: 2.666667vw 3.2vw 0;
  font-size: 0.8rem;
}
.tags {
  padding-bottom: 2.666667vw;
  border-bottom: 1px solid #eee;
}
.tags li {
  color: #6d7885;
  background-color: #ebf5ff;
  display: inline-block;
  padding: 0 2.4vw;
  height: 7.466667vw;
  line-height: 7.466667vw;
  margin: 0.933333vw;
  font-size: 0.7rem;
  border-radius: 0.533333vw;
}

.unsatisfied {
  color: #aaa !important;
  background-color: #f5f5f5 !important;
}

.comments-wrap > li {
  padding: 4vw 0 3.2vw;
  border-bottom: 0.133333vw solid #eee;
  display: flex;
}
.comment-user {
  width: 10.666667vw;
}
.comment-user img {
  width: 30px;
  height: 30px;
  border-radius: 50%;
}
.comments-info {
  flex: 1;
  font-size: 0.9rem;
}
.comment-name {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.comment-name h4 {
  margin-top: 0;
  color: #333;
  margin-right: 1.6vw;
}
.comment-name small {
  font-size: 0.6rem;
  color: #999;
}
.comment-rating {
  display: flex;
  margin: 1.6vw 0 0.533333vw;
  align-items: center;
}
.comment-rating > span {
  font-size: 0.6rem;
  margin-left: 1.066667vw;
}
.comment-text {
  color: #333;
  word-break: break-word;
  margin: 2.133333vw 0;
}
.comment-reply {
  margin: 2.666667vw 0;
  padding: 2.666667vw;
  border-radius: 1.066667vw;
  background: #f3f3f3;
  position: relative;
  font-size: 0.8rem;
}
.comment-reply::after {
  content: " ";
  position: absolute;
  bottom: 100%;
  left: 4vw;
  width: 0;
  height: 0;
  border-style: solid;
  border-width: 0 2.133333vw 2.133333vw;
  border-color: transparent transparent #f3f3f3;
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
</style>
