<template>
    <div class="user_acct right align_left" v-once>
        <p class="right">{{ authenticatedUser.username }} <span class="avatar_icon"></span></p>
        <div class="inner_nav">
            <p class="border_bottom">
                <span class="weight_bold block_disp eliptic">{{ authenticatedUser.fullname }}</span>
                <span class="block_disp small_faint">{{ authenticatedUser.username }}</span>
            </p>
            <div>
                <span v-for="(submenu, key) in menu" v-bind:key="key">
                    <a :href="submenu.url" :class="`${submenu.color}`">{{key}}</a>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            authenticatedUser: Object,
            menu: Object,
        },
        data() {
            return {
                
            };
        },
        mounted() {
            console.log('Header Component mounted.');
        },
        methods: {
            
        }
    }
</script>