<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3 d-flex align-items-center justify-content-between" v-if="isProjectOwner">
                    <h3 class="font-weight-bold">{{ projectName }}</h3>
                    <button type="button" v-if="wasGroupsLoaded" class="mb-2 btn btn-primary"
                            @click=showCreateGroupModal><i class="fa fa-plus d-md-inline-block d-none"></i> Add group
                    </button>
                </div>

                <div class="my-5" v-if="!groups.length && wasGroupsLoaded">
                    <empty-component
                            :title="'No group!'"
                            :description="'You can create your first group.'"
                    >

                    </empty-component>
                </div>


                <table class="table" v-if="groups.length">
                    <thead class="thead-light">
                    <tr>
                        <th scope="col" class="w-50">Name</th>
                        <th scope="col" class="w-20" v-if="isProjectOwner">Info</th>
                        <th scope="col" class="w-30 text-right" v-if="isProjectOwner">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="group in groups">
                        <td>
                            <span class="d-flex align-items-center">
                                <router-link class="text-decoration-none mr-3 text-black-50" :to="{ path: '/projects/groups/'+ group.id }"><i class="fa fa-folder-o fa-3x"></i></router-link>
                                <router-link class="text-decoration-none text-dark" :to="{ path: '/projects/groups/'+ group.id }">{{
                                    group.name }}
                                </router-link>
                            </span>
                        </td>
                        <td v-if="isProjectOwner">
                        <span class="text-black-50">
                            <span><i class="fa fa-user"></i> {{ group.users ?
                            group.users.length : 0 }}</span><br>
                            <span><i class="fa fa-key"></i> {{ group.credentials ?
                                group.credentials.length : 0 }}</span>
                        </span>
                        </td>
                        <td class="text-right action-btn-area" v-if="isProjectOwner">
                            <button class="btn btn-sm btn-success" @click="showShareModal(group, $event)"><i
                                    class="fa fa-share-alt"></i></button>
                            <button class="btn btn-sm btn-primary" @click="showEditModal(group, $event)"><i
                                    class="fa fa-edit"></i></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!--create modal-->
        <div class="modal fade" id="create_group_modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Create group
                        </h5>
                        <button type="button" class="btn-modal-close" data-dismiss="modal">x</button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form role="form" @submit.prevent="createGroup" method="POST" id="createGroupForm">

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right">Name</label>

                                <div class="col-md-6">
                                    <input placeholder="eg. Marketing" type="text" id="create-team-name"
                                           class="form-control" name="name" v-model="createGroupForm.name" required/>
                                </div>
                            </div>

                            <!-- Create Button -->
                            <div class="form-group row mb-0">
                                <div class="offset-md-4 col-md-6">
                                    <button :disabled="disableCreateBtn" type="submit" class="btn btn-primary">
                                        Create
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!--edit modal-->
        <div class="modal fade" id="edit_group_modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Update group
                        </h5>
                        <button type="button" class="btn-modal-close" data-dismiss="modal">x</button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form role="form" @submit.prevent="updateGroup" method="POST" id="updateGroupForm">
                            <!-- Name -->
                            <div :class="responseType" v-if="updateGroupForm.successful">
                                @{{ responseMsg }}
                            </div>

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right">Name</label>

                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="name" v-model="updateGroupForm.name"/>
                                </div>
                            </div>

                            <!-- Create Button -->
                            <div class="form-group row mb-0">
                                <div class="offset-md-4 col-md-6">
                                    <button type="submit" class="btn btn-primary" :disabled="disableUpdateBtn">
                                        Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!--share modal-->
        <div class="modal fade" id="share_group_modal" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Share group
                        </h5>
                        <button type="button" class="btn-modal-close" data-dismiss="modal">x</button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <div class="user-selection-area mb-4">
                            Share with:
                            <select id="sharableUserId" name="id">
                                <option value="" selected="selected" disabled>Select User</option>
                            </select>
                            <button class="btn btn-sm btn-primary" :disabled="disableShareBtn" @click="shareGroup">
                                <i class="fa fa-share-alt"></i> Share
                            </button>
                        </div>

                        <div class="sharedUsers">
                            <table class="table">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="user in groupUsers">
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td>
                                        <button type="button" @click="deleteGroupUser(user.id, $event)"
                                                class="btn btn-sm btn-danger">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
                projectName: '',
                groups: [],
                createGroupForm: {
                    name: ''
                },
                updateGroupForm: {
                    id: '',
                    name: '',
                },
                shareGroupForm: {
                    id: '',
                    name: '',
                },
                groupUsers: [],
                wasGroupsLoaded: false,
                disableCreateBtn: false,
                disableUpdateBtn: false,
                disableShareBtn: false,
                disableDeleteUserBtn: false,
                isProjectOwner: false,
            };
        },
        watch: {
            '$route.params': {
                handler() {
                    this.getGroups()
                },
                immediate: true,
            }
        },
        mounted() {

        },
        beforeMount() {
            this.getGroups();
            this.checkProjectOwnership();
        },
        methods: {
            showCreateGroupModal: function (event) {
                if (event) event.preventDefault();
                this.createGroupForm.name = "";
                $('#create_group_modal').modal('show');
            },
            showEditModal: function (group, event) {
                event.preventDefault();
                this.updateGroupForm.id = group.id;
                this.updateGroupForm.name = group.name;
                $('#edit_group_modal').modal('show');
            },
            showShareModal: function (group, event) {
                event.preventDefault();
                // load user select option
                $('#sharableUserId').select2({
                    dataType: 'json',
                    minimumInputLength: 3,
                    dropdownAutoWidth: true,
                    placeholder: 'Search user',
                    ajax: {
                        url: 'ajax/groups/' + group.id + '/shareable-users?forSelect2=true',
                        // url: 'https://jsonplaceholder.typicode.com/posts',
                        dataType: 'json',
                        type: "GET",
                        quietMillis: 50,
                        processResults: function (response) {
                            return {
                                results: response.data
                            };
                        }
                    }
                });

                this.shareGroupForm.name = group.name;
                this.shareGroupForm.id = group.id;
                this.getGroupUsers();
                $('#share_group_modal').modal('show');
            },
            createGroup: function (event) {
                event.preventDefault();
                this.disableCreateBtn = true;
                axios.post("/ajax/projects/" + this.$route.params.projectId + "/groups", this.createGroupForm)
                    .then(response => {
                        swal({
                            text: response.data.message,
                            icon: response.data.status,
                        });

                        if (response.data.status == 'success') {
                            this.getGroups()
                        }
                    })
                    .catch(error => {
                        let message = "Invalid data!";
                        if (error.response.data.message) message = error.response.data.message;
                        swal({
                            text: message,
                            icon: "error",
                        });
                    })
                    .finally(() => {
                        this.disableCreateBtn = false;
                        this.createGroupForm.name = "";
                        $('#create_group_modal').modal('hide');
                    });
            },
            shareGroup: function (event) {
                event.preventDefault();
                this.disableShareBtn = true;
                axios.post("/ajax/groups/" + this.shareGroupForm.id + "/users", {'id': $("#sharableUserId").val()})
                    .then(response => {
                        swal({
                            text: response.data.message,
                            icon: response.data.status,
                        });

                        if (response.data.status == 'success') {
                            this.getGroupUsers()
                            this.getGroups()
                        }
                    })
                    .catch(error => {
                        let message = "Invalid data!";
                        if (error.response.data.message) message = error.response.data.message;
                        swal({
                            text: message,
                            icon: "error",
                        });
                    })
                    .finally(() => {
                        this.disableShareBtn = false;
                        $('#sharableUserId').val(null).trigger('change');
                    });
            },
            deleteGroupUser: function (userId, event) {
                event.preventDefault();
                this.disableDeleteUserBtn = true;
                axios.delete("/ajax/groups/" + this.shareGroupForm.id + "/users/" + userId, {'id': userId})
                    .then(response => {
                        swal({
                            text: response.data.message,
                            icon: response.data.status,
                        });

                        if (response.data.status == 'success') {
                            this.getGroupUsers()
                            this.getGroups()
                        }
                    })
                    .catch(error => {
                        let message = "Invalid data!";
                        if (error.response.data.message) message = error.response.data.message;
                        swal({
                            text: message,
                            icon: "error",
                        });
                    })
                    .finally(() => {
                        this.disableDeleteUserBtn = false;
                    });
            },
            updateGroup: function () {
                this.disableUpdateBtn = true;
                axios.put('ajax/projects/' + this.$route.params.projectId + '/groups/' + this.updateGroupForm.id, this.updateGroupForm)
                    .then(response => {
                        swal({
                            text: response.data.message,
                            icon: response.data.status,
                        });
                        if (response.data.status == "success") {
                            this.getGroups();
                            $('#edit_group_modal').modal('hide');
                        }
                    })
                    .catch(error => {
                        let message = "Invalid data!";
                        if (error.response.data.message) message = error.response.data.message;
                        swal({
                            text: message,
                            icon: "error",
                        });
                    })
                    .finally(() => {
                        this.disableUpdateBtn = false;
                        this.updateGroupForm.name = "";
                        this.updateGroupForm.id = "";
                        $('#edit_group_modal').modal('hide');
                    });
            },
            getGroups: function () {
                axios.get("/ajax/auth/projects/" + this.$route.params.projectId + "/groups")
                    .then(response => {
                        this.projectName = response.data.data.project_name;
                        this.groups = response.data.data.groups;
                        // sync sidebar projects
                        this.$store.dispatch('setProjects');
                    })
                    .finally(() => {
                        this.wasGroupsLoaded = true;
                    });
            },
            getGroupUsers: function () {
                axios.get("/ajax/groups/" + this.shareGroupForm.id + "/users")
                    .then(response => {
                        this.groupUsers = response.data.data;
                    });
            },
            checkProjectOwnership: function () {
                axios.get('ajax/auth/projects/' + this.$route.params.projectId)
                    .then(response => {
                        if (response.data.status == "success") this.isProjectOwner = true;
                    })
                    .catch(error => {
                        //todo: show error page
                        this.isProjectOwner = false;
                    });
            },
        }
    }
</script>
