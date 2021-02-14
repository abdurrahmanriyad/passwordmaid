<template>
    <div class="container" v-if="dashboardData">
        <div class="row justify-content-center stats-area">
            <div class="col-md-3">
                <router-link class="text-decoration-none text-dark" :to="{ path: '/projects'}">
                    <div class="card bg-white mb-3 shadow-sm">
                        <div class="card-body">
                            <h2 class="card-title mb-2">{{ dashboardData.total_projects }}</h2>
                            <h5 class="card-text text-black-50">
                                <small>total</small>
                                <br>Project(s)
                            </h5>
                        </div>
                    </div>
                </router-link>
            </div>

            <div class="col-md-3">
                <router-link class="text-decoration-none text-dark" :to="{ path: '/projects'}">
                    <div class="card bg-white mb-3 shadow-sm">
                        <div class="card-body">
                            <h2 class="card-title">{{ dashboardData.total_groups }}</h2>
                            <h5 class="card-text text-black-50">
                                <small>total</small>
                                <br>Group(s)
                            </h5>
                        </div>
                    </div>
                </router-link>
            </div>

            <div class="col-md-3">
                <router-link class="text-decoration-none text-dark" :to="{ path: '/projects'}">
                    <div class="card bg-white mb-3 shadow-sm">
                        <div class="card-body">
                            <h2 class="card-title">{{ dashboardData.shared_groups }}</h2>
                            <h5 class="card-text  text-black-50">Group(s)<br>
                                <small>Shared</small>
                            </h5>
                        </div>
                    </div>
                </router-link>
            </div>

            <div class="col-md-3">
                <router-link class="text-decoration-none text-dark" :to="{ path: '/projects'}">
                    <div class="card bg-white mb-3 shadow-sm">
                        <div class="card-body">
                            <h2 class="card-title">{{ dashboardData.shared_with_me }}</h2>
                            <h5 class="card-text text-black-50">Group(s)<br>
                                <small>Shared with you</small>
                            </h5>
                        </div>
                    </div>
                </router-link>
            </div>
        </div>

        <div class="row mt-md-5 mt-3">
            <div class="col-md-4">
                <h5 class="mb-3">Shared users</h5>
                <div class="card bg-white mb-1 shadow-sm" v-for="user in dashboardData.shared_users">
                    <div class="card-body py-2 d-flex align-items-center">
                        <div class="d-inline-block">
                            <h6 class="card-title mb-1">{{ user.name }}</h6>
                            <h6 class="card-text text-black-50">{{ user.email }}</h6>
                        </div>
                    </div>
                </div>
                <div class="text-black-50" v-if="!(dashboardData.shared_users.length)">Nothing shared!</div>
            </div>

            <div class="col-md-4 mt-md-0 mt-4">
                <h5 class="mb-3">Recent groups</h5>
                <router-link class="text-decoration-none recent-groups text-dark"
                             v-for="group in dashboardData.recent_groups" :key="group.id"
                             :to="{ path: '/projects/groups/' + group.id}">
                    <div class="card border-radius-0 border-bottom-0 outline-0">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="my-0">{{ group.name }}</h6>
                                </div>
                                <div class="col-md-6 text-right text-black-50">
                                    <span class="d-inline-block mr-3"><i class="fa fa-users"></i> <strong>{{ group.total_credentials }}</strong></span>
                                    <span><i
                                            class="fa fa-key"></i> <strong>{{ group.total_credentials }}</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </router-link>
                <div class="text-black-50" v-if="!(dashboardData.recent_groups.length)">No recent group!</div>
            </div>

            <div class="col-md-4 mt-md-0 mt-4">
                <h5 class="mb-3">Recently shared with me</h5>
                <router-link class="text-decoration-none recent-groups text-dark"
                             v-for="group in dashboardData.recent_shared_by_groups" :key="group.id"
                             :to="{ path: '/projects/groups/' + group.id}">
                    <div class="card border-radius-0 border-bottom-0 outline-0">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="my-0">{{ group.name }}</h6>
                                </div>
                                <div class="col-md-6 text-right text-black-50">
                                    <span><i
                                            class="fa fa-key"></i> <strong>{{ group.total_credentials }}</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </router-link>
                <div class="text-black-50" v-if="!(dashboardData.recent_shared_by_groups.length)">Nothing shared!</div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user'],
        data() {
            return {
                dashboardData: null
            };
        },
        mounted() {

        },
        beforeMount() {
            this.getDashboardData();
        },
        methods: {
            getDashboardData: function () {
                // this.wasGroupsLoaded = true;
                axios.get("/ajax/dashboard-data")
                    .then(response => {
                        this.dashboardData = response.data.data;
                    })
                    .finally(() => {
                        // this.wasGroupsLoaded = true;
                    });
            },
        }
    }
</script>

<style>

</style>
