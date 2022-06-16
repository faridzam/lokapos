<template>
  <div class="navbar-main">
        <div class="navbar-left">
            <ul class="navbar-left-list">
                <li class="navbar-left-list-items">
                    <router-link to="penjualan">PoS</router-link>
                </li>
                <li class="navbar-left-list-items">
                    <router-link to="invoices">Invoices</router-link>
                </li>
                <li class="navbar-left-list-items">
                    <router-link to="item-sales">Item Sales</router-link>
                </li>
                <li class="navbar-left-list-items">
                    <router-link to="saved-cart">Saved Cart({{savedCartQuantity}})</router-link>
                </li>
            </ul>
        </div>

        <div class="navbar-center">
            <span class="saloka-loco">
                <svg-vue icon="logo-saloka" style="width: 10rem; height: auto;"></svg-vue>
            </span>
            <div class="close-order-modal">
            <closeOrderModal
            v-show="isCloseOrderModalVisible"
            ref="closeOrderModal"
            @closeCloseOrderModal="closeCloseOrderModal"
            />
        </div>
        </div>

        <div class="navbar-right">
            <button class="navbar-right-button cash-drawer-button cursor-pointer" @click="openCashDrawer()">
                <span>
                    <svg-vue icon="cash-drawer-icon" style="width: auto; height: 1.5rem;"></svg-vue>
                </span>
            </button>
            <button class="navbar-right-button refresh-button cursor-pointer" @click="reloadPage()">
                <span>
                    <svg-vue icon="refresh-icon" style="width: auto; height: 1.5rem;"></svg-vue>
                </span>
            </button>
            <button class="navbar-right-button closed-order-button cursor-pointer" @click="showCloseOrderModal()">
                <span class="sleepy-icon-span">
                    <svg-vue icon="sleepy-icon" style="width: auto; height: 2rem; filter: brightness(0) invert(1);"></svg-vue>
                    <p>CLOSED ORDER</p>
                </span>
            </button>
        </div>
  </div>
</template>

<script>
import closeOrderModal from './closeOrderModal.vue';

export default {
    props: {
    //   user: {
    //     type: Number,
    //     required: true
    //   }
    },
    data: ()=> {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            savedCartQuantity: 0,
            isCloseOrderModalVisible: false,
        }
    },
    components:{
        closeOrderModal,
    },
    mounted(){
        this.getData();
        Echo.channel('savedCartCount')
        .listen('savedCartUpdated', (e) => {
            this.savedCartQuantity = e.savedCartCount;
        })
    },
    methods: {
        logout : function(){
            this.$refs.form.submit();
        },
        getData(){
            axios.get('api/countSavedCart')
                 .then((response)=>{
                   this.savedCartQuantity = response.data.quantity;
                 });
        },
        openCashDrawer(){
            //
            axios.post('openCashDrawer')
                 .then((response)=>{
                    console.log(response);

                    Vue.$toast.open({
                        message: 'cash drawer opened!',
                        type: 'success',
                        position: 'top',
                        duration: 2000,
                    });

                 }).catch(error=>{
                   //
                   console.log(error);
                   Vue.$toast.open({
                        message: 'Cash Drawer not Opened!',
                        type: 'error',
                        position: 'top',
                        duration: 2000,
                    });
                 });
        },
        reloadPage(){
            //
            location.reload();
        },
        showCloseOrderModal() {
            this.isCloseOrderModalVisible = true;
            this.$refs.closeOrderModal.getData();
        },
        closeCloseOrderModal() {
            this.isCloseOrderModalVisible = false;
        },
    }
}

</script>

<style scoped>
    .navbar-main{
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        height: 4rem;
    }

    .navbar-left{
        display: flex;
        justify-content: flex-start;
        align-items: center;
        padding: 0;
        margin-left: 4%;
        height: 100%;
        width: 30%;
    }
    .navbar-left-list{
        display: flex;
        justify-content: flex-start;
        padding: 0;
        list-style: none;
    }
    .navbar-left-list-items{
        margin-right: 3rem;
        font-size: 18px;
        font-weight: 500;
    }
    .navbar-left-list-items a{
        color: rgb(44, 44, 44);
        text-decoration: none;
        width: 100%;
    }
    .navbar-left-list-items a.router-link-active{
        color: #F39F19;
        border-radius: 10px;
        text-decoration: none;
    }

    .navbar-center{
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
    }

    .navbar-right{
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-right: 4%;
        height: 100%;
        width: 30%;
    }
    .navbar-right-button{
        height: 3rem;
        margin-left: 1rem;
        border-radius: 10px;
    }
    .closed-order-button{
        width: 10rem;
        background-color: #f45049;
        border: 0px solid #f45049;
    }
    .refresh-button{
        width: 3rem;
        background-color: #73c1a9;
        border: 0px solid #73c1a9;
    }
    .cash-drawer-button{
        width: 3rem;
        background-color: #73c1a9;
        border: 0px solid #73c1a9;
    }
    .sleepy-icon-span{
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
        color: white;
    }
    .sleepy-icon-span svg{
        display: flex;
    }
    .sleepy-icon-span p{
        display: flex;
        font-size: 13px;
        font-weight: 550;
    }
</style>
