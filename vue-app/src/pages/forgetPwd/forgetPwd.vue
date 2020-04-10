<template>
    <div id="forgetPwd">
        <ul class="pwd_list">
            <li class="pwd_info" v-for="(item, index) in list" :key="index" @click="turn_page(index)">
                <p class="title">{{item.title}}</p>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    components:{
        
    },
    name: 'forgetPwd',
    data () {
        return {
            list: [{
                title: '忘记登录密码',
            },{
                title: '忘记安全密码',
            },{
                title: '忘记银行密码'
            }]
        }
    },
    created() {
        
    },
    methods: {
        turn_page(index) {
            let idx = Number(index + 1)
            this.$router.push({
                path: '/retrievePwd',
                query: {
                    type: idx
                }
            })
        }
    }
}
</script>

<style scoped>
    #forgetPwd{
        width: 100%;
        /* height: 100%; */
        position: absolute;
        top: 44px;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #F2F2F2;
        overflow: hidden;
    }
    .pwd_list{
        width: 100%;
        height: 100%;
        margin-top: 26px;
    }
    .pwd_info{
        width: 351px;
        height: 48px;
        background-color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px auto;
    }
    .title{
        color: #4A4130;
        font-size: 17px;
        line-height: 24px;
        font-weight: 500;
    }
</style>