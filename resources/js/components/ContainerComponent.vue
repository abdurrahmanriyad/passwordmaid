<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <aside class="sidebar-container" id="sidebar-container">
                    <div class="sidebar-close d-md-none" @click="closeSidebar">&times;</div>
                    <ul class="sidebar-menu">
                        <li id="home-menu">
                            <router-link :to="{ path: '/' }" class="dropdown-title ml-3"><i class="fa fa-home"></i><span>Home</span></router-link>
                        </li>
                        <li class="has-dropdown">
                            <router-link :to="{ path: '/projects' }" class="dropdown-title" @click.native="loadProjects"><span> <i class="fa fa-angle-right"></i> <i class="fa fa-th-large"></i>Projects</span></router-link>
                            <ul class="menu-dropdown">
                                <li class="has-dropdown" v-for="project in projects">
                                    <router-link class="dropdown-title m-0 p-0" :to="{ path: '/projects/'+ project.id }" @click.native="loadGroups">
                                        <i v-if="project.groups.length" class="fa fa-angle-right"></i>
                                        <span v-if="!project.groups.length">
                                            <i :class="project.owner ? 'fa fa-folder-o ml-14px' : 'fa fa-share-alt-square ml-14px'"></i><span class="project-name">{{ project.name }}</span>
                                        </span>
                                        <span v-else>
                                            <i :class="project.owner ? 'fa fa-folder-o' : 'fa fa-share-alt-square'"></i><span class="project-name">{{ project.name }}</span>
                                        </span>

                                    </router-link>
                                    <ul class="menu-dropdown group-dropdown">
                                        <li v-for="group in project.groups">
                                            <router-link :to="{ path: '/projects/groups/'+ group.id }" class="text-decoration-none">
                                                <i class="fa fa-folder"></i><span class="group-name mr-1">{{ group.name }}</span> ({{ group.credentials.length }})
                                            </router-link>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </aside>
            </div>

            <div class="col-md-9">
                <div class="bg-white px-2 py-4">
                    <router-view :user="user">

                    </router-view>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user'],
        data() {
            return {

            };
        },
        computed: {
            projects () {
                return this.$store.getters.projects;
            }
        },
        mounted() {
            $(".btn-project-opt").dropdown();
        },
        beforeMount() {
            if (this.user === 'undefined' || this.user === null || !this.user) {
                window.location.reload(true);
            }
        },
        methods: {
            expandProjects: function (event) {
                let item = $(event.target);
                event.stopPropagation();
                item.closest("li").toggleClass('open');
                item.closest(".dropdown-title").next().toggle();
            },
            expandGroups: function (event) {
                let item = $(event.target);
                event.stopPropagation();
                item.closest("li").toggleClass('open');
                item.closest(".dropdown-title").next().toggle();
            },

            loadProjects: function (event) {
                event.preventDefault();
                let item = $(event.target);
                event.stopPropagation();
                item.closest("li").toggleClass('open open-page');
                item.closest(".dropdown-title").next().toggle();

                if ($(window).width() < 500) {
                    $(".sidebar-menu .project-name").text(function(index, currentText) {
                        return (currentText.length > 14) ? currentText.substr(0, 14) + '...' : currentText;
                    });
                }
            },

            loadGroups: function (event) {
                let item = $(event.target);
                event.stopPropagation();
                item.closest("li").toggleClass('open open-page');
                item.closest(".dropdown-title").next().toggle();

                if ($(window).width() < 500) {
                    $(".sidebar-menu .group-name").text(function(index, currentText) {
                        return (currentText.length > 12) ? currentText.substr(0, 12) + '...' : currentText;
                    });
                }
            },

            closeSidebar: function () {
                $("#sidebar-container").removeClass('open');
            }
        }
    }
</script>
