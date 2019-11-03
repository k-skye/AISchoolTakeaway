<template>
  <div @click.self="hideView">
    <!-- 导航 -->
    <div
      v-if="filterData"
      class="filter_wrap"
    >
      <aside class="filter">
        <div
          v-for="(item,index) in filterData"
          :key="index"
          class="filter-nav"
          :class="{'filter-bold':currentFilter==index}"
          @click="filterSort(index)"
        >
          <span>{{ item.name }}</span>
        </div>
      </aside>
    </div>
  </div>
</template>

<script>
export default {
  name: "FilterView",
  props: {
    filterData: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      currentFilter: 0
    };
  },
  methods: {
    filterSort(index) {
      this.currentFilter = index;
      switch (index) {
        case 0:
          this.$emit("update", {
            condition: this.filterData[0].condition
          });
          break;
        case 1:
          this.$emit("update", {
            condition: this.filterData[1].condition
          });
          break;
        case 2:
          this.$emit("update", {
            condition: this.filterData[2].condition
          });
          break;
        case 3:
          this.$emit("update", {
            condition: this.filterData[3].condition
          });
          break;
        default:
          break;
      }
    }
  }
};
</script>

<style scoped>
.filter_wrap {
  background: #fff;
  position: sticky;
  top: 54px;
  z-index: 10;
}
.filter {
  position: relative;
  border-bottom: 1px solid #ddd;
  line-height: 10.4vw;
  z-index: 101;
  height: 10.666667vw;
  display: flex;
  justify-content: space-around;
}
.filter-nav {
  flex: 1;
  text-align: center;
  color: #666;
  font-size: 0.8333rem;
}

.filter-bold {
  font-weight: 600;
  color: #333;
}
</style>
