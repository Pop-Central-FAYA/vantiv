<template>
    <div v-once>
        <div class="_nav">
            <ul>
                <div v-for="(menu, key) in sideMenuBody" v-bind:key="key">
                    <li :class="`${menu.icon_class+' '+menu.active_class}`"><a :href="menu.url">{{key}}</a></li>
                </div>
            </ul>
        </div>

        <div class="_nav_button">
            <div v-for="(menu, key) in sideMenuFooter" v-bind:key="key">
                <a :href="menu.url" class="btn full block_disp uppercased align_center mb3"><span :class="`${menu.icon_class}`"></span>{{key}}</a>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            sideMenuBody: Object,
            sideMenuFooter: Object,
        },
        data() {
            return {
            };
        },
        mounted() {
            console.log('Side Menu Component mounted.');
        },
        methods: {
            
        }
    }
</script>