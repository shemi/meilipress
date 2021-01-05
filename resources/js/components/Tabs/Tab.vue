<template>
  <div class="mp-tab" v-if="id === $parent.activeTabId">

    <slot></slot>

  </div>
</template>

<script>
  export default {

    props: {
      id: {
        type: String,
        required: true
      },

      label: {
        type: String,
        required: true
      }
    },

    data() {
      return {

      }
    },

    mounted() {
      this.$parent.registerTab(this.id, this.label);
    },

    methods: {

    }

  }
</script>

<style scoped>

</style>
