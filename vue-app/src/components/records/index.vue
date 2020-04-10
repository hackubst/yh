<template>
    <div id="record">
        <ul class="relief_list">
            <li class="relief_info" :class="{dual: index%2 != 0}" v-for="(i, index) in relief_list" :key="index">
                <div>{{i.addtime | formatDateYearDot}}</div>
                <div>{{i.card_name}}</div>
                <div>{{i.num}}</div>
                <!-- <div>删除</div> -->
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    components:{
        
    },
    props: {
        relief_list: {
            type: Array,
            default: ''
        }
    },
    name: 'record',
    data () {
        return {
            
        }
    },
    created() {
        
    },
    methods: {

    }
}
</script>

<style scoped>
    #record{
        width: 100%;
        height: auto;
        /* height: 100%; */
        /* position: absolute;
        top: 82px;
        left: 0;
        right: 0;
        bottom: 0; */
    }
    .mescroll {
        height: auto;
    }
    .relief_list{
        width: 100%;
        height: auto;
        background-color: #fff;
    }
    .relief_info {
        width: 100%;
        height: 48px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }
    .dual{
        background-color: #FFF7EC;
    }
    .relief_info div{
        width: 125px;
        height: 48px;
        line-height: 48px;
        text-align: center;
        font-size:14px;
        font-weight:400;
        color:rgba(74,65,48,1);
    }
</style>