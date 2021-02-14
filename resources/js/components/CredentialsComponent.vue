<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3 d-flex align-items-center justify-content-between" v-if="isProjectOwner">
                    <h3 class="font-weight-bold"><i class="fa fa-users d-none d-md-inline-block"></i> {{ groupName }}
                    </h3>
                    <button type="button" v-if="isProjectOwner && wasCredentialsLoaded" class="mb-2 btn btn-primary"
                            @click=showCreateModal><i class="fa fa-plus d-md-inline-block d-none"></i> Add New
                    </button>
                </div>
                <div class="my-5" v-if="!credentials.length && wasCredentialsLoaded">
                    <empty-component
                            :title="'No credential!'"
                            :description="'You can create your first credential above.'"
                    >

                    </empty-component>
                </div>
                <div class="credentials" v-if="credentials.length">
                    <div class="card shadow-sm mb-3" v-for="credential in credentials" :key="credential.id">
                        <div class="card-body">
                            <h3 class="card-title border-bottom pb-3"><i
                                    class="fa fa-folder-o d-md-inline-block d-none"></i> {{ credential.title }}</h3>

                            <div class="credItems">
                                <div class="cred-item mb-3" v-for="(customField, index) in credential.custom_fields">
                                    <h6 class="card-subtitle mb-1 text-muted font-weight-light">{{ customField.name
                                        }}</h6>
                                    <div class="card-text">
                                        <span v-if="!customField.showCred">*****
                                            <span class="text-primary cursor-pointer" v-clipboard:success="credCopySuccess"
                                               v-clipboard.prevent="customField.value"><i class="fa fa-copy"></i>
                                            </span>
                                        </span>
                                        <span v-else title="click to copy">{{ customField.value }}
                                            <span class="text-primary cursor-pointer" v-clipboard:success="credCopySuccess"
                                               v-clipboard.prevent="customField.value"><i class="fa fa-copy"></i>
                                            </span>
                                        </span>
                                        <button class="hideEncrypted btn btn-sm btn-primary"
                                                v-if="customField.is_encrypted"
                                                @click="showEncryptedValue(customField)">{{ customField.showCred ?
                                            'Hide' : 'Show' }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="border-top pt-3 cred-action">
                                <a href="#" v-if="isProjectOwner" class="card-link mr-2"
                                   @click="showUpdateCredModal(credential)">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <span v-if="isProjectOwner">
                                    <a href="#" v-if="credential.is_private == 0" class="card-link mr-2"
                                       @click="changeCredAccessibility(credential, $event)">
                                        <i class="fa fa-lock"></i> Make Private
                                    </a>
                                    <a href="#" v-else class="card-link mr-2"
                                       @click="changeCredAccessibility(credential, $event)">
                                        <i class="fa fa-unlock"></i> Make Public
                                    </a>
                                </span>
                                <a href="#" v-if="isProjectOwner" class="card-link"
                                   @click="deleteCred(credential, $event)">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--create modal-->
            <div class="modal fade" id="create_cred_modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h5 class="modal-title">
                                Add Credential
                            </h5>
                            <button type="button" class="btn-modal-close" data-dismiss="modal">x</button>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body">
                            <form @submit.prevent="createCred" id="createCredForm">
                                <div class="form-group">
                                    <input type="text" name="title" class="form-control" placeholder="Title">
                                </div>

                                <div class="form-group text-right">
                                    <input type="checkbox" name="is_private" class="mr-2">
                                    <label class="col-form-label d-inline-block text-md-left">Private?</label>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">Credential(s)</label>
                                    <div id="credentials">
                                        <div class="credential-item">
                                            <div class="credential-input">
                                                <div class="moveCredRow mr-3"><i class="fa fa-arrows"></i></div>
                                                <select name="keys[]" class="mr-2">
                                                    <option v-for="(item, index) in credKeys"
                                                            :selected="item.key == 'username'" :key="index"
                                                            :value="item.key">{{ item.value }}
                                                    </option>
                                                </select>
                                                <input type="text" placeholder="value" class="px-2 mr-2"
                                                       name="values[]" required>
                                                <button type="button" class="btn btn-sm btn-danger removeCredRow">
                                                    X</button>
                                            </div>
                                            <div class="text-right">
                                                <div class="form-group">
                                                    <input type="hidden" value="0" name="is_encrypted[]">
                                                    <input type="checkbox" class="changeIsEncrypted mr-2"
                                                           name="is_encrypted[]">
                                                    <label class="col-form-label d-inline-block text-md-left">Encrypted?</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="credential-item">
                                            <div class="credential-input">
                                                <div class="moveCredRow mr-3"><i class="fa fa-arrows"></i></div>
                                                <select name="keys[]" class="mr-2">
                                                    <option v-for="(item, index) in credKeys"
                                                            :selected="item.key == 'password'" :key="index"
                                                            :value="item.key">{{ item.value }}
                                                    </option>
                                                </select>
                                                <input type="text" placeholder="value" class="px-2 mr-2"
                                                       name="values[]" required>
                                                <button type="button" class="btn btn-sm btn-danger removeCredRow">
                                                    X</button>
                                            </div>
                                            <div class="text-right">
                                                <div class="form-group">
                                                    <input type="hidden" value="0" name="is_encrypted[]">
                                                    <input type="checkbox" class="changeIsEncrypted mr-2"
                                                    name="is_encrypted[]">
                                                    <label class="col-form-label d-inline-block text-md-left">Encrypted?</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button id="addCredRow" type="button" @click="addRow" class="addCredRow mt-2">
                                            <i class="fa fa-plus-circle fa-2x"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Create Button -->
                                <hr>
                                <div class="form-group mb-0">
                                    <div class="text-right">
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                        <button type="submit" :disabled="disableCreateBtn" id="btnSubmitCredCreate"
                                                class="btn btn-primary">
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!--update modal-->
            <div class="modal fade" id="update_cred_modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h5 class="modal-title">
                                Edit Credential
                            </h5>

                            <button type="button" class="btn-modal-close" data-dismiss="modal">x</button>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body" v-if="viewingCredItem">
                            <form role="form" @submit.prevent="updateCred" action="" method="POST" id="updateCredForm">
                                <div class="form-group">
                                    <label class="col-form-label d-inline-block mr-2 text-md-left">Title</label>
                                    <input type="text" name="title" :value="viewingCredItem.title" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label">Credential(s)</label>
                                    <div id="updateCredentials">
                                        <div class="credential-item"
                                             v-for="customField in viewingCredItem.custom_fields">
                                            <div class="credential-input">
                                                <div class="moveCredRow mr-3"><i class="fa fa-arrows"></i></div>
                                                <select name="keys[]" class="mr-2">
                                                    <option v-for="(item, index) in credKeys"
                                                            :selected="item.key == customField.name" :key="index"
                                                            :value="item.key">{{ item.value }}
                                                    </option>
                                                </select>
                                                <input type="text" placeholder="value" :value="customField.value"
                                                       name="values[]" class="px-2 mr-2" required>
                                                <button type="button" class="btn btn-sm btn-danger removeCredRow">
                                                    X</button>
                                            </div>
                                            <div class="text-right">
                                                <div class="form-group">
                                                    <input type="hidden" v-if="customField.is_encrypted == 1" disabled
                                                    value="0" name="is_encrypted[]">
                                                    <input type="hidden" v-else value="0" name="is_encrypted[]">
                                                    <input type="checkbox" class="changeIsEncrypted mr-2"
                                                    :checked="customField.is_encrypted" name="is_encrypted[]">
                                                    <label class="col-form-label d-inline-block text-md-left">Encrypted?</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button id="addCredRowToUpdate" class="addCredRow" @click="addUpdateCredRow" type="button">
                                            <i class="fa fa-plus-circle fa-2x"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Create Button -->
                                <hr>
                                <div class="form-group mb-0">
                                    <div class="text-right">
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                        <button type="submit" :disabled="disableUpdateBtn" id="btnSubmitCredUpdate"
                                                class="btn btn-primary">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                disableCreateBtn: false,
                disableUpdateBtn: false,
                viewingCredItem: null,
                groupName: '',
                credentials: [],
                credKeys: [],
                isProjectOwner: false,
                wasCredentialsLoaded: false
            };
        },
        mounted() {
            // fixes credential Item order after update
            $('#update_cred_modal').on('hidden.bs.modal', () => {
                this.viewingCredItem = null;
            });

        },
        beforeMount() {
            this.getCredKeys();
            this.getCredentials();
            this.checkGroupOwnership();
        },
        watch: {
            '$route.params': {
                handler() {
                    this.getCredentials();
                    this.checkGroupOwnership();
                },
                immediate: true,
            }
        },
        methods: {
            showCreateModal: function (event) {
                if (event) event.preventDefault();
                this.resetCreateForm();
                $(".additional").remove();
                $('#create_cred_modal').modal('show');
                $('#create_cred_modal').on('shown.bs.modal', function () {
                    $("#credentials").sortable({handle: '.moveCredRow'});
                    $("#credentials").disableSelection();
                })
            },
            showUpdateCredModal: function (credential) {
                this.viewingCredItem = credential;
                if (event) event.preventDefault();
                $(".additional").remove();
                $('#update_cred_modal').modal('show');
                $('#update_cred_modal').on('shown.bs.modal', function () {
                    $("#updateCredentials").sortable({handle: '.moveCredRow'});
                    $("#updateCredentials").disableSelection();
                })
            },
            showEncryptedValue: function (customField) {
                customField.showCred = !customField.showCred;
            },
            nullViewingIitem: function () {
                this.viewingCredItem = null;
            },
            addRow: function () {
                let credentialsElem = $("#credentials");
                credentialsElem.append(this.getCredItemElem);
            },
            credCopySuccess: function () {
                alert('copied!');
            },
            addUpdateCredRow: function () {
                let credentialsElem = $("#updateCredentials");
                credentialsElem.append(this.getCredItemElem);
            },
            getCredKeys: function () {
                this.wasCredentialsLoaded = false;
                axios.get('/ajax/cred-keys')
                    .then(response => {
                        this.credKeys = response.data.data;
                    }).finally(() => {
                    this.wasCredentialsLoaded = true
                });
            },
            getCredentials: function () {
                axios.get('/ajax/auth/groups/' + this.$route.params.groupId + '/credentials')
                    .then(response => {
                        this.addShouldHideKeyToCreds(response.data.data);
                        this.groupName = response.data.data.group_name;
                        this.credentials = this.appendShowCredKey(response.data.data.credentials)
                        this.$store.dispatch('setProjects');
                    }).finally(() => {
                    this.wasCredentialsLoaded = true
                });
            },
            appendShowCredKey: function (credentials) {
                credentials.forEach(function (credential) {
                    credential.custom_fields.forEach(function (customField) {
                        customField.showCred = !customField.is_encrypted;
                    });
                });
                return credentials;
            },
            createCred: function (event) {
                event.preventDefault();
                this.disableCreateBtn = true;
                let actionUrl = '/ajax/groups/' + this.$route.params.groupId + '/credentials';
                let data = $(event.target).serialize();
                axios.post(actionUrl, data)
                    .then(response => {
                        swal({
                            text: response.data.message,
                            icon: response.data.status,
                        });

                        if (response.data.status == 'success') this.getCredentials()
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
                        $('#create_cred_modal').modal('hide');
                    });
            },
            deleteCred: function (credential, event) {
                event.preventDefault();
                if (!confirm("Do you really want to delete?")) return;

                let targetElem = $(event.currentTarget);
                targetElem.prop('disabled', true);
                let actionUrl = '/ajax/groups/' + this.$route.params.groupId + '/credentials/' + credential.id;
                axios.delete(actionUrl)
                    .then(response => {
                        swal({
                            text: response.data.message,
                            icon: response.data.status,
                        });

                        if (response.data.status == 'success') this.getCredentials()
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
                        targetElem.prop('disabled', false);
                    });
            },
            changeCredAccessibility: function (credential, event) {
                event.preventDefault();
                if (!confirm("Are you sure?")) return;
                let targetElem = $(event.currentTarget);
                targetElem.prop('disabled', true);
                let actionUrl = '/ajax/groups/' + this.$route.params.groupId + '/credentials/' + credential.id + '/accessibility';
                axios.put(actionUrl, {'is_private': ((credential.is_private == 0) ? 1 : 0)})
                    .then(response => {
                        swal({
                            text: response.data.message,
                            icon: response.data.status,
                        });

                        if (response.data.status == 'success') this.getCredentials()
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
                        targetElem.prop('disabled', false);
                    });
            },
            updateCred: function (event) {
                event.preventDefault();
                this.disableUpdateBtn = true;
                let actionUrl = '/ajax/groups/' + this.$route.params.groupId + '/credentials/' + this.viewingCredItem.id;
                let data = $(event.target).serialize();
                axios.put(actionUrl, data)
                    .then(response => {
                        swal({
                            text: response.data.message,
                            icon: response.data.status,
                        });

                        if (response.data.status == 'success') {
                            this.getCredentials()
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
                        $('#update_cred_modal').modal('hide');
                    });
            },
            addShouldHideKeyToCreds: function (creds) {
                for (let i = 0; i < creds.length; i++) {
                    for (let j = 0; j < creds[i].custom_fields.length; j++) {
                        if (creds[i].custom_fields[j].is_encrypted) {
                            creds[i].custom_fields[j].showCred = false;
                        } else {
                            creds[i].custom_fields[j].showCred = true;
                        }
                    }
                }
            },
            checkGroupOwnership: function () {
                axios.get('ajax/auth/groups/' + this.$route.params.groupId)
                    .then(response => {
                        if (response.data.status == "success") this.isProjectOwner = true;
                    })
                    .catch(error => {
                        //todo: show error page
                        this.isProjectOwner = false;
                    });
            },
            resetCreateForm: function () {
                $("#createCredForm input[type=text]").val("");
                $('#createCredForm input[type=checkbox]').prop('checked', false);
            },
            getCredItemElem: function () {
                let elem = `
                <div class="credential-item additional">
                    <div class="credential-input">
                        <div class="moveCredRow mr-3"><i class="fa fa-arrows"></i></div>
                        <select name="keys[]" class="mr-2">`;
                for (let index in this.credKeys) {
                    elem += "<option value='" + this.credKeys[index].key + "'>" + this.credKeys[index].value + "</option>";
                }
                elem += `</select>
                        <input type="text" placeholder="value" class="px-2 mr-2"
                               name="values[]" required>
                        <button type="button" class="btn btn-sm btn-danger removeCredRow">
                                                    X</button>
                    </div>
                    <div class="text-right">
                        <div class="form-group">
                            <input type="hidden" value="0"  name="is_encrypted[]">
                            <input type="checkbox" class="changeIsEncrypted mr-2" name="is_encrypted[]">
                            <label class="col-form-label d-inline-block text-md-left">Encrypted?</label>
                        </div>
                    </div>
                </div>
            `;

                return elem;
            },
        }
    }
</script>

<style>
    span.hideEncrypted {
        background: #3e7f9e;
        font-size: 11px;
        padding: 2px 5px;
        border-radius: 4px;
        color: #fff;
        margin-left: 5px;
        display: inline-block;
        cursor: pointer;
    }

    .credential-input {
        display: flex;
        /* text-align: center; */
        align-content: space-between;
        justify-content: center;
        margin-top: 10px;
    }

    .moveCredRow i {
        margin-top: 7px;
    }

    .moveCredRow {
        padding: 0 7px;
    }

    .credential-input input {
        width: 100%;
    }

    .cursor-pointer {
        cursor: pointer;
    }
</style>
