import axios from "axios/index";

export default {
    state: {
        projects: [],
        groups: [],
    },
    mutations: {
        setProjects (state, projects) {
            state.projects = projects;
        }
    },
    actions: {
        async setProjects({commit}) {
            return new Promise((resolve, reject) => {
                axios.get('/ajax/auth/projects')
                    .then(response => {
                        commit('setProjects', response.data.data)
                        return resolve(null);
                    })
                    .catch(error => {
                        commit('setProjects', []);
                        return reject(error);
                    })
            })
        },
    },
    getters: {
        projects: (state) => {
            return state.projects;
        }
    }
}