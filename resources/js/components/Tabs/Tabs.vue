<template>
  <div class="mp-tabs">

    <nav class="mp-tabs-handlers nav-tab-wrapper" ref="tabs-handlers">
        <a :href="'#'+tab.id" v-for="tab in tabs"
           :key="tab.id"
           :id="`mp-tab-trigger-${tab.id}`"
           class="nav-tab"
           :class="{'nav-tab-active': tab.id === activeTabId}">
          {{ tab.label }}
        </a>
    </nav>

    <div class="mp-tabs-content" :id="activeTabId">
      <slot></slot>
    </div>

  </div>
</template>

<script>
  const $ = window.jQuery;

  export default {

    props: {},

    data() {
      return {
        tabs: [],
        activeTabId: null
      }
    },

    mounted() {
      this.activateDefaultTab();

      window.addEventListener("hashchange", this.onHashChange.bind(this), false);
    },

    beforeDestroy() {
      window.removeEventListener('hashchange', this.onHashChange.bind(this));
    },

    methods: {

      registerTab(id, label) {
        this.tabs.push({id, label});
      },

      activateDefaultTab() {
        const tab = this.getHashTab() || this.tabs[0];

        if (tab) {
          this.activateTab(tab.id);
        }
      },

      activateTab(tabId) {
        this.activeTabId = tabId;

        this.$nextTick(() => {
          this.centerItVariableWidth(this.activeTabId);
        });
      },

      centerItVariableWidth(tabId) {
        const $out = $(this.$refs['tabs-handlers']);
        const $target = $(`#pg-tab-trigger-${tabId}`);
        const x = $out.width();
        const y = $target.outerWidth(true);
        const z = $target.index();
        const m = $out.find('li');
        let q = 0;

        for (let i = 0; i < z; i++) {
          q += $(m[i]).outerWidth(true);
        }

        $out.animate({
          scrollLeft: -Math.max(0, q - (x - y) / 2)
        }, 200);
      },

      getHashTab() {
        const hash = (window.location.hash || '').replace('#', '');

        if (!hash) {
          return null;
        }

        return this.tabs.find(tab => tab.id === hash);
      },

      onHashChange() {
        const tab = this.getHashTab();

        if (!tab) {
          return;
        }

        this.activateTab(tab.id);
      }

    }

  }
</script>

<style lang="scss" scoped>

  .mp-tabs {
    position: relative;

    .mp-tabs-content {
        max-width: 800px;
    }

  }

</style>
