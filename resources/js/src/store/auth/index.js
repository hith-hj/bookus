import { $themeBreakpoints } from '@themeConfig'
import axios from 'axios';

export default {
    namespaced: true,
    state: {
        userToken :null,
        user :null,
        user_name :'',

        isAdmin: true,
        isLogin:true,
        loading:false,

        permission:[],
    },
    getters: {

        isLogged(state){
            return !!state.userToken;
        },
        isAdmin(state){
            return state.isAdmin ;

        },
        loading(state){

            return state.loading ;
        },
        isLogin(state){
            return state.isLogin;
        },
        getUser(state){
            
            return JSON.parse(localStorage.getItem("user"))
        },
        getNavbar(state){
            console.log(state.navbar)
            return state.navbar;
        },
        isExistPermission:(state) => (perm) =>{
            return state.permission.includes(perm);
        },
        allPerm:(state) => (perm) =>{
            return state.permission;
        }
    },
    mutations: {
        setUserToken(state,userToken){
            state.userToken=userToken;
            localStorage.setItem("userToken",JSON.stringify(userToken));
            axios.defaults.headers.common.Authoriztion =`bearer ${userToken}`
        },
        removeUserToken(state){
            state.userToken= null;
            localStorage.removeItem('userToken')
        },
        setUser(state,user){
            localStorage.setItem("user",JSON.stringify(user));

        },
        setLoading(state,loading){
            state.loading = loading;

        },
        setIsLogin(state,status){
            state.isLogin = status;
        },
        setPermission(state,perm){
            state.permission = perm;
        },
        removeUser(state){
            state.user= null;
            localStorage.removeItem('user')
        },
        setAdmin(state){
            state.isAdmin = true ;


        },

        removeAdmin(state){
            state.isAdmin= false;
            localStorage.removeItem('isAdmin')
        },
    },
    actions: {
        RegisterUser({commit},payload){
            axios.post('',payload)
                .then(res =>{
                    console.log(res)
                    commit('setUserToken',res.data.content.token)
                })
                .catch(err=>{
                    console.log(err)
                })
        },
        async LoginUser({commit},payload){
            await   axios.post('/api/admin/login',payload)
                .then(res =>{
                    commit('setUserToken',res.data.content.token)
                    commit('setUser',res.data.content.user)
                    commit('setPermission',res.data.content.permission)
                    commit('setIsLogin',true)


                    if(res.data.content.user.user_type === "admin")
                    {
                        commit('setAdmin')
                    }
                }).catch(err=>{
                    return 'error'
                })
        }
    },
}
