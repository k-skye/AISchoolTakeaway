<template>
  <transition name="fade">
    <div
      v-show="showInfoModel"
      class="info-model"
    >
      <div class="brief-modal">
        <h2>
          <span>{{ shopInfo.name }}</span>
        </h2>
        <ul>
          <li>
            <h3>{{ shopInfo.stars }}</h3>
            <p>评分</p>
          </li>
          <li>
            <h3>{{ shopInfo.salesNum }}</h3>
            <p>月售</p>
          </li>
          <li>
            <h3>{{ shopInfo.deliveryTime }}分钟</h3>
            <p>配送时间</p>
          </li>

          <li>
            <h3>¥{{ shopInfo.deliveryFee }}元</h3>
            <p>配送费</p>
          </li>
          <li>
            <h3>{{ shopInfo.location }}</h3>
            <p>位置</p>
          </li>
        </ul>
        <!--         <h3 class="promotion">
          <span>公告</span>
        </h3> 
        <p class="brief-modal-yx">{{shopInfo.note}}</p>-->
      </div>
      <!-- x号 -->
      <div
        class="brief-modal-close"
        @click="$emit('close')"
      >
        <van-icon
          name="close"
          color="white"
          size="25px"
        />
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  props: {
    shopInfo: {
      type: Object,
      default: () => {}
    },
    showInfoModel: Boolean
  }
};
</script>

<style scoped>
.info-model {
  position: fixed;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  background: rgba(0, 0, 0, 0.5);
  z-index: 99;
}
.brief-modal {
  position: relative;
  width: 90%;
  max-height: 85.333333vw;
  padding: 7.066667vw 6.666667vw 7.466667vw;
  border-radius: 1.066667vw;
  overflow: hidden;
  background: #fff;
  z-index: 100;
  display: flex;
  flex-direction: column;
  box-sizing: border-box;
}
.brief-modal h2 {
  font-size: 1.4rem;
  line-height: 6.666667vw;
  color: #333;
  text-align: center;
  font-weight: bolder;
}
.brief-modal ul {
  display: flex;
  margin: 5.066667vw -6.666667vw 0;
}
.brief-modal ul li {
  flex: 1;
  text-align: center;
}
.brief-modal ul li h3 {
  font-size: 0.9rem;
  font-weight: 600;
  color: #333;
  margin-bottom: 1.6vw;
}
.brief-modal ul li p {
  font-size: 0.8rem;
  color: #999;
}
.brief-modal .promotion {
  position: relative;
  text-align: center;
  margin: 4.8vw auto 2.666667vw;
  width: 20.266667vw;
  background-image: linear-gradient(90deg, #fff, #333 50%, #fff);
  background-size: 100% 1px;
  background-position: 50%;
  background-repeat: no-repeat;
}
.brief-modal .promotion span {
  font-size: 0.8rem;
  padding: 0 1.066667vw;
  color: #999;
  background-color: #fff;
}
.brief-modal-yx {
  font-size: 0.8rem;
  line-height: 1.54;
  color: #333;
  max-height: 26.666667vw;
  overflow-y: auto;
}
.brief-modal-close {
  margin-top: 8vw;
  width: 8vw;
  height: 8vw;
}
.brief-modal-close img {
  width: 100%;
  height: 100%;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease-out;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>
