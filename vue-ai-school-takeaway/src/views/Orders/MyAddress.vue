<template>
  <div class="myAddress">
    <Header :isLeft="true" :title="title" />

    <!-- 显示收货地址 -->
    <div class="address-view">
      <div class="address-card" v-for="(address,index) in allAddress" :key="index">
        <div class="address-card-select">
          <i class="fa fa-check-circle" v-if="selectIndex == index"></i><!-- 这里可以改成默认 -->
        </div>

        <div class="address-card-body" @click="setAddressInfo(address,index)">
          <p class="address-card-title">
            <span class="username">{{address.name}}</span>
            <span v-if="address.gender" class="gender">{{address.gender == 1 ? '先生' : '小姐'}}</span>
            <span class="phone">{{address.phone}}</span>
          </p>
          <p class="address-card-address">
            <span class="address-text">{{address.dormitory}} {{address.roomNum}}</span>
          </p>
        </div>
        <div class="address-card-edit">
          <i @click="handleEdit(address)" class="fa fa-edit"></i>
          <i @click="handleDelete(address,index)" class="fa fa-close"></i>
        </div>
      </div>
    </div>

    <!-- 新增收货地址 -->
    <div class="address-view-bottom" @click="addAddress">
      <i class="fa fa-plus-circle"></i>
      <span>新增收货地址</span>
    </div>
  </div>
</template>

<script>
import Header from "../../components/Header";
export default {
  name: "MyAddress",
  data() {
    return {
      title: "我的地址",
      allAddress: [],
      selectIndex: 0
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => vm.getData());
  },
  computed: {
    userInfo() {
      return this.$store.getters.userInfo;
    }
  },
  methods: {
    addAddress() {
      this.$router.push({
        name: "addAddress",
        params: {
          title: "添加地址",
          addressInfo: {
            name: "",
            gender: "",
            phone: "",
            dormitory: "",
            roomNum: ""
          },
          userID: this.userInfo.id
        }
      });
    },
    getData() {
      this.$axios("https://takeawayapi.pykky.com/?s=Address.GetUserAddrs", {
        params: {
          userID: this.userInfo.id
        }
      }).then(res => {
        this.allAddress = res.data.data;
      });
    },
    handleEdit(address) {
      this.$router.push({
        name: "addAddress",
        params: {
          title: "编辑地址",
          addressInfo: address
        }
      });
    },
    handleDelete(address, index) {
      this.$axios
        .post("https://takeawayapi.pykky.com/?s=Address.RemoveAddr", {
            id: address.id
        })
        .then(res => {
          this.allAddress.splice(index, 1);
        });
    },
    setAddressInfo(address, index) {
      this.selectIndex = index;
      // 将address对象存储到vuex
      this.$store.dispatch("setAddrInfo", address);
      this.$router.push("/settlement");
    }
  },
  components: {
    Header
  }
};
</script>

<style scoped>
.myAddress {
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  padding-top: 45px;
}

.address-view-bottom {
  position: fixed;
  height: 13.866667vw;
  bottom: 0;
  width: 100%;
  background: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
  border-top: 0.266667vw solid #ddd;
  color: #3190e8;
  font-size: 1rem;
}
.address-view-bottom > i {
  font-size: 1.3rem;
  margin-right: 8px;
}

.address-view {
  height: 100%;
  overflow-y: auto;
  padding-bottom: 13.866667vw;
}
.address-card {
  background-color: #fff;
  padding: 4vw;
  border-bottom: 1px solid #ddd;
  display: flex;
  min-height: 18.133333vw;
}
.address-card-body {
  flex: 1;
  overflow: hidden;
}
.address-card-title {
  font-size: 1rem;
  color: #666;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  padding-bottom: 1.066667vw;
}
.address-card-title .username {
  color: #333;
  font-weight: 700;
}
.address-card-title .gender {
  padding: 0 1.6vw 0 0.8vw;
}
.address-card-address {
  color: #666;
  font-size: 0.84rem;
  display: flex;
  align-items: center;
}
.address-text {
  line-height: 4.533333vw;
}

/* 编辑和删除 */
.address-card-edit {
  flex-basis: 13.066667vw;
  display: flex;
  justify-content: space-around;
  align-items: center;
}
.address-card-edit > i {
  color: #aaa;
  font-size: 1.2rem;
}

/*  选中图标 */
.address-card-select {
  flex-basis: 7.333333vw;
  min-width: 7.333333vw;
  display: flex;
  align-items: center;
}
.address-card-select > i {
  font-size: 1.5rem;
  color: #4cd964;
}
</style>
