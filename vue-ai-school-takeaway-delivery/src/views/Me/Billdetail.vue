<template>
  <div class="billdetail">
    <div class="header">
      <van-nav-bar
        title="账单明细"
        left-arrow
        @click-left="$router.push('wallet')"
      />
    </div>
    <div class="contain">
      <van-list
        v-model="loading"
        :finished="finished"
        finished-text="没有更多了"
        @load="onLoad"
      >
        <van-cell-group
          v-for="(trad,index) in trads"
          :key="index"
        >
          <van-cell
            :title="trad.date"
            icon="clock-o"
          >
            {{ trad.type }}
            {{ trad.done == 0 && trad.type == '提现' ? ' (待到账)':'' }}
            {{ trad.done == 0 && trad.type == '配送费' ? ' (待确认收货)':'' }}
            <div
              v-if="(trad.money > 0)"
              slot="default"
              class="right-in"
            >
              + ¥{{ parseFloat(trad.money).toFixed(2) }}
            </div>
            <div
              v-else
              slot="default"
              class="right-out"
            >
              - ¥{{ Math.abs(parseFloat(trad.money).toFixed(2)) }}
            </div>
          </van-cell>
        </van-cell-group>
      </van-list>
    </div>
  </div>
</template>

<script>
export default {
  name: "Billdetail",
  data() {
    return {
      loading: false,
      finished: false,
      offset: 1,
      size: 5,
      trads:[] //存放数据容器
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.firstLoadData();
    });
  },
  computed: {
    userInfo() {
      return this.$store.getters.userInfo;
    }
  },
  methods:{
    firstLoadData() {
      this.offset = 1;
      this.finished = false;
      // 拉取商家信息
      this.$axios(
        "https://takeawayapi.pykky.com/?s=Tradinglog.GetOnesAllTradLog",
        {
          params: {
            deliverID: this.userInfo.id,
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
        this.trads = res.data.data;
      });
    },
    onLoad() {
      // 异步更新数据
      setTimeout(() => {
        if (!this.finished) {
          this.offset += (parseInt(this.trads[0].id));
          // 拉取商家信息
          this.$axios(
            "https://takeawayapi.pykky.com/?s=Tradinglog.GetOnesAllTradLog",
            {
              params: {
                deliverID: this.userInfo.id,
                offset: this.offset,
                limit: this.size
              }
            }
          ).then(res => {
            if (JSON.stringify(res.data.data) != "{}") {
              res.data.data.forEach(element => {
                this.trads.push(element);
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
$green: #07c160;
$red: #ee0a24;
.billdetail {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  .contain{
    .right-in{
      color: $green;
    }
    .right-out{
      color: $red;
    }
  }
}
</style>