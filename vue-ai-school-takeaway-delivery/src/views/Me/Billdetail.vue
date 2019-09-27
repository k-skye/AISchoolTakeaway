<template>
  <div class="billdetail">
    <div class="header">
      <van-nav-bar title="账单明细" left-arrow @click-left="$router.push('wallet')" />
    </div>
    <div class="contain">
      <van-list v-model="loading" :finished="finished" finished-text="没有更多了" @load="onLoad">
        <van-cell-group v-for="item in list" :key="item" >
          <van-cell title="08-23 12:48" icon="clock-o">
            <div class="right-in" v-if="true" slot="default">+ ¥2.0</div>
            <div class="right-out" v-else slot="default">- ¥13.00</div>
          </van-cell>
        </van-cell-group>
      </van-list>
    </div>
  </div>
</template>

<script>
export default {
  name: "billdetail",
  data() {
    return {
      loading: false,
      finished: false,
      list: []
    };
  },
  methods:{
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