import { forEach, reduce } from "lodash";
import createPersistedState from "vuex-persistedstate"

let store = {
    plugins: [createPersistedState()],
    state: {
        cart: [],
        savedCart: [],
        cartCount: 0,
        savedCartCount: 0,
    },

    getters:{
        // noteGetter: (state)=> {
        //     //
        //     let inputs = [{}];

        //     state.modalNotes.forEach(value => {
        //         inputs.push({
        //             notes: value,
        //         });
        //     });

        //     return inputs;
        // },
    },

    mutations: {
        submitNoteModal(state, payload){

            let found = state.cart.find(product => product.id == payload.item.id);

            console.log(payload.inputKeyArray)

            if (found) {

                found.notes = payload.inputKeyArray;

            } else {

                state.cart.push(item);

                Vue.set(item, 'quantity', 1);
                Vue.set(item, 'notes', payload.inputKeyArray);
                Vue.set(item, 'discount', 0);
                Vue.set(item, 'specialPrice', 0);
                Vue.set(item, 'totalPrice', item.price);

            }

        },
        submitDiscountModal(state, payload){

            let found = state.cart.find(product => product.id == payload.item.id);

            console.log(payload.inputDiscount)

            if (found) {

                found.specialPrice = 0;
                found.discount = Number(payload.inputDiscount);

                let price = found.price * (100-found.discount) / 100;
                found.totalPrice = found.quantity * price;

            } else {

                state.cart.push(item);

                Vue.set(item, 'quantity', 1);
                Vue.set(item, 'notes', []);
                Vue.set(item, 'discount', payload.inputDiscount);
                Vue.set(item, 'specialPrice', 0);
                Vue.set(item, 'totalPrice', item.price);

            }

        },
        submitSpecialPriceModal(state, payload){

            let found = state.cart.find(product => product.id == payload.item.id);

            console.log(payload.inputSpecialPrice)

            if (found) {

                found.discount = 0;

                if (payload.inputSpecialPrice === 0) {
                    //
                    found.specialPrice = Number(payload.inputSpecialPrice);
                    found.totalPrice = found.quantity * found.price;
                } else if (payload.inputSpecialPrice === "") {
                    //
                    found.specialPrice = Number(payload.inputSpecialPrice);
                    found.totalPrice = found.quantity * found.price;
                } else if (payload.inputSpecialPrice > found.price) {
                    console.log('special price lebih tinggi dari harga');
                    found.specialPrice = 0;
                    found.totalPrice = found.quantity * found.price;
                } else {
                    found.specialPrice = Number(payload.inputSpecialPrice);
                    found.totalPrice = found.quantity * found.specialPrice;
                }

            } else {

                state.cart.push(item);

                Vue.set(item, 'quantity', 1);
                Vue.set(item, 'notes', []);
                Vue.set(item, 'discount', 0);
                Vue.set(item, 'specialPrice', payload.inputSpecialPrice);
                Vue.set(item, 'totalPrice', item.price);

            }

        },
        addToCart(state, item) {
            let found = state.cart.find(product => product.id == item.id);

            if (found) {

                if (found.specialPrice > 0) {
                    found.quantity ++;
                    found.totalPrice = found.quantity * found.specialPrice;
                } else if (found.discount > 0) {
                    let price = found.price * (100-found.discount) / 100;
                    found.quantity ++;
                    found.totalPrice = found.quantity * price;
                } else{
                    found.quantity ++;
                    found.totalPrice = found.quantity * found.price;
                }

            } else {

                state.cart.push(item);

                Vue.set(item, 'quantity', 1);
                Vue.set(item, 'notes', Array());
                Vue.set(item, 'discount', 0);
                Vue.set(item, 'specialPrice', 0);
                Vue.set(item, 'totalPrice', item.price);

            }

            state.cartCount++;
        },
        removeFromCart(state, item) {
            let index = state.cart.indexOf(item);

            if (index > -1) {
                let product = state.cart[index];
                state.cartCount -= product.quantity;

                state.cart.splice(index, 1);
            }
        },
        qtyDecrement(state, item) {
            let found = state.cart.find(product => product.id == item.id);
            let index = state.cart.indexOf(item);

            if (found) {

                if (found.quantity < 2) {
                    let product = state.cart[index];
                    state.cartCount -= product.quantity;

                    state.cart.splice(index, 1);
                } else{

                    if (found.specialPrice > 0) {
                        found.quantity --;
                        found.totalPrice = found.quantity * found.specialPrice;
                    } else if (found.discount > 0) {
                        let price = found.price * (100-found.discount) / 100;
                        found.quantity --;
                        found.totalPrice = found.quantity * price;
                    } else{
                        found.quantity --;
                        found.totalPrice = found.quantity * found.price;
                    }

                    state.cartCount--;
                }

            }
        },
        qtyIncrement(state, item) {
            let found = state.cart.find(product => product.id == item.id);

            if (found) {

                if (found.specialPrice > 0) {
                    found.quantity ++;
                    found.totalPrice = found.quantity * found.specialPrice;
                } else if (found.discount > 0) {
                    let price = found.price * (100-found.discount) / 100;
                    found.quantity ++;
                    found.totalPrice = found.quantity * price;
                } else{
                    found.quantity ++;
                    found.totalPrice = found.quantity * found.price;
                }

                state.cartCount++;
            }
        },
        setSavedCart(state, carts){
            //
            var result = [];

            for(var i in carts){
                result.push(carts[i]);
                state.savedCartCount++;
            }

            state.savedCart = result;

            console.log(result);
        },
        clearCart(state){
            //
            state.cart = [];
            state.cartCount = 0;
        },
        clearSavedCart(state){
            //
            state.savedCart = [];
            state.savedCartCount = 0;
        },
        // modalNoteItem(state, item){
        //     state.modalItem = item;
        //     state.modalNotes = [];

        //     state.modalNotes.push(item.notes);
        // },
    },

};

export default store;
