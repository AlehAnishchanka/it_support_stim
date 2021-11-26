var it_support_form = new Vue({
    el: '#it_support_form',
    data: {
        textButton: "Кнопка"
    },
    mounted: function () {
        this.$root.$on('bv::dropdown::show', bvEvent => {
          if(bvEvent.componentId === 'dropdown-2') {
            this.isDropdown2Visible = true;
          }
          })
        this.$root.$on('bv::dropdown::hide', bvEvent => {
          if(bvEvent.componentId === 'dropdown-2') {
            this.isDropdown2Visible = false;
          }
          if(this.isDropdown2Visible) {
            bvEvent.preventDefault()
          }
          })
      }
})