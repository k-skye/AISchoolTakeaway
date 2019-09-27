<template>
  <div class="protocol">
    <div class="header">
      <van-nav-bar title="协议" left-arrow @click-left="$router.push('me')" />
    </div>
  </div>
</template>

<script>
export default {
  name: "protocol",
  data() {
    return {
    };
  }
};
</script>

<style lang="scss" scoped>
.protocol {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
}
</style>