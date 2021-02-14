<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <h3 class="font-weight-bold">Projects</h3>
                    <button type="button" v-if="wasProjectsLoaded" class="mb-2 btn btn-primary"
                            @click=showCreateProjectModal><i class="fa fa-plus d-md-inline-block d-none"></i> Add project
                    </button>
                </div>

                <div class="my-5" v-if="!projects.length && wasProjectsLoaded">
                    <empty-component
                            :title="'No projects!'"
                            :description="'You can create your first project above.'"
                    >

                    </empty-component>
                </div>

                <div class="projects" v-if="projects.length">
                    <table class="table">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col" class="w-50">Name</th>
                            <th scope="col" class="w-30 text-right">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="project in projects">
                            <td>
                                <span class="d-flex align-items-center">
                                    <router-link class="text-decoration-none mr-3 text-black-50" :to="{ path: '/projects/'+ project.id }"><i class="fa fa-folder-o fa-3x"></i></router-link>
                                    <router-link class="text-decoration-none text-dark" :to="{ path: '/projects/'+ project.id }">{{ project.name }}</router-link>
                                </span>
                            </td>
                            <td class="text-right action-btn-area">
                                <button type="button" v-if="isOwner(project)" @click=showUpdateProjectModal(project)
                                        class="btn btn-sm btn-info"><i
                                        class="fa fa-edit"></i></button>
                                <div class="d-inline" v-if="isOwner(project)">
                                    <button :disabled="disableDeleteBtn"
                                            @click="deleteProject(project)" type="button"
                                            class="btn btn-sm btn-warning">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                                <router-link :to="{ path: '/projects/'+ project.id }">
                                    <button type="button" class="btn btn-sm btn-info">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </router-link>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!--create modal-->
        <div class="modal fade" id="create_project_modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Create project
                        </h5>
                        <button type="button" class="btn-modal-close" data-dismiss="modal">x</button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form role="form" method="POST" id="createProjectForm">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right">Name</label>

                                <div class="col-md-6">
                                    <input placeholder="eg. My Travel Company" v-model="createProjectForm.name"
                                           type="text" id="create-team-name" class="form-control" required/>
                                    <span class="help-block">
                            </span>
                                </div>
                            </div>

                            <!-- Create Button -->
                            <div class="form-group row mb-0">
                                <div class="offset-md-4 col-md-6">
                                    <button type="submit" :disabled="disableCreateBtn" class="btn btn-primary"
                                            @click="createProject">
                                        Create
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!--update modal-->
        <div class="modal fade" id="update_project_modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Update project
                        </h5>
                        <button type="button" class="btn-modal-close" data-dismiss="modal">x</button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form role="form" @submit.prevent="updateProject" method="POST" id="updateProjectForm">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right">Name</label>
                                <div class="col-md-6">
                                    <input type="text" id="edit-team-name" class="form-control" name="name"
                                           v-model="updateProjectForm.name" required/>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="offset-md-4 col-md-6">
                                    <button type="submit" :disabled="disableUpdateBtn" class="btn btn-primary">Update
                                    </button>
                                </div>
                            </div>
                        </form>
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
                createProjectForm: {
                    name: ""
                },
                updateProjectForm: {
                    id: null,
                    name: ""
                },
                wasProjectsLoaded: false,
                disableCreateBtn: false,
                disableUpdateBtn: false,
                disableUpdateStatusBtn: false,
                disableDeleteBtn: false,
            };
        },
        computed: {
            projects () {
                return this.$store.getters.projects;
            }
        },
        mounted() {

        },
        beforeMount() {
            this.getProjects();
        },
        methods: {
            showCreateProjectModal: function (event) {
                event.preventDefault();
                $('#create_project_modal').modal('show');
            },
            showUpdateProjectModal: function (project) {
                this.updateProjectForm.id = project.id;
                this.updateProjectForm.name = project.name;
                $('#update_project_modal').modal('show');
            },
            createProject: function (event) {
                event.preventDefault();
                this.disableCreateBtn = true;
                let actionUrl = '/ajax/projects';
                axios.post(actionUrl, this.createProjectForm)
                    .then(response => {
                        swal({
                            text: response.data.message,
                            icon: response.data.status,
                        });

                        if (response.data.status == 'success') this.getProjects()
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
                        this.createProjectForm.name = "";
                        $('#create_project_modal').modal('hide');
                    });
            },
            updateProject: function () {
                if (!confirm("Do you really want to update?")) return;
                this.disableUpdateBtn = true;
                axios.put('ajax/projects/' + this.updateProjectForm.id, this.updateProjectForm)
                    .then(response => {
                        swal({
                            text: response.data.message,
                            icon: response.data.status,
                        });
                        if (response.data.status == "success") {
                            this.getProjects();
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
                        this.createProjectForm.name = "";
                        this.createProjectForm.id = "";
                        $('#update_project_modal').modal('hide');
                    });
            },
            updateProjectStatus: function (project) {
                if (!confirm("Do you really want to update?")) return;
                this.disableUpdateStatusBtn = false;
                axios.put('ajax/projects/' + project.id + '/status', {'active': Number(!(Number(project.active)))})
                    .then(response => {
                        swal({
                            text: response.data.message,
                            icon: response.data.status,
                        });
                        if (response.data.status == "success") this.getProjects()
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
                        this.disableUpdateStatusBtn = false;
                    });
            },
            deleteProject: function (project) {
                if (!confirm("Do you really want to delete?")) return;
                this.disableDeleteBtn = false;
                axios.delete('ajax/projects/' + project.id)
                    .then(response => {
                        swal({
                            text: response.data.message,
                            icon: response.data.status,
                        });
                        if (response.data.status == "success") this.getProjects()
                    })
                    .catch(error => {
                        let message = "Something went wrong!";
                        if (error.response.data.message) message = error.response.data.message;
                        swal({
                            text: message,
                            icon: "error",
                        });
                    })
                    .finally(() => {
                        this.disableDeleteBtn = false;
                    });
            },
            getProjects: function () {
                this.wasProjectsLoaded = false;
                this.$store.dispatch('setProjects').then(res => {
                    this.wasProjectsLoaded = true;
                }).catch(err => {
                    this.wasProjectsLoaded = true;
                });
            },
            isOwner: function (project) {
                if (project.owner && project.owner.id == this.user.id) {
                    return true;
                }
                return false;
            }
        }
    }
</script>
