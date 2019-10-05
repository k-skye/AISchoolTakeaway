<template>
  <section class="checkout-section">
    <div class="delivery">
      <div class="deliver-left">
        <span class="delivery-time">送达时间</span>
      </div>
      <div class="delivery-right">
        <span class="delivery-select">尽快送达 ({{deliveryTime(restInfo.deliveryTime)}}送达)</span>
        <i class="fa fa-angle-right"></i>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "Delivery",
  props: {
    restInfo: Object
  },
  methods: {
    deliveryTime(time) {
      let date = new Date();
      time = parseInt(time);
      date.setMinutes(date.getMinutes() + time);
      return date.getHours() + ":" + date.getMinutes();
    }
  }
};
</script>

<style scoped>
.checkout-section {
  margin-bottom: 2.133333vw;
  padding: 0 5.333333vw;
  background: #fff;
  box-shadow: 0 0.133333vw 0.266667vw 0 rgba(0, 0, 0, 0.05);
}
.delivery {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-top: 1px dotted #eee;
  padding: 4.266667vw 0;
}
.delivery-left {
  flex: 1;
  display: flex;
  flex-direction: column;
}
.delivery-time {
  font-size: 1rem;
  font-weight: 500;
}
.delivery-right {
  text-align: right;
}
.delivery-select {
  text-align: right;
  color: #2395ff;
}
.delivery-right > i {
  margin-left: 0.666667vw;
  color: #888;
  font-size: 1.2rem;
}
</style>
