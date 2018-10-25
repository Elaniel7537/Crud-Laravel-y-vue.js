<template>
    <v-container grid-list-xs>
        <v-layout row wrap>
            <v-flex xs12>
                <v-card color="blue lighten-4">
                    <v-card-title primary-title>
                        <div>
                            <h3 class="headline mb-0">
                                Usuarios
                                <v-tooltip bottom>
                                    <v-btn
                                        slot="activator"
                                        color="indigo"
                                        fab 
                                        dark 
                                        small 
                                        @click="openDialog()"
                                    >
                                        <v-icon dark>add</v-icon>
                                    </v-btn>
                                    <span>Agregar</span>
                                </v-tooltip>
                            </h3>
                            <div>Usuarrios Disponibles</div>
                        </div>
                    </v-card-title>
                    <v-text-field
                        label="Buscar"
                        prepend-icon="search"
                        @input="getData()"
                        v-model="search"
                    ></v-text-field>
                    <v-card-text>
                        <v-data-table
                            :headers="headers"
                            :items="items"
                            :loading="loading"
                            hide-actions
                            class="elevation-1"
                        >
                            <template slot="items" slot-scope="props">
                                <td>{{ props.item.name }}</td>
                                <td>{{ props.item.email }}</td>
                                <td>
                                    <v-tooltip bottom>
                                        <v-btn
                                            slot="activator"
                                            color="warning"
                                            fab 
                                            dark 
                                            small 
                                            @click="openDialog(props.item)"
                                        >
                                            <v-icon dark>edit</v-icon>
                                        </v-btn>
                                        <span>Editar</span>
                                    </v-tooltip>
                                    <v-tooltip bottom>
                                        <v-btn
                                            slot="activator"
                                            color="red"
                                            fab 
                                            dark 
                                            small 
                                            @click="deleteItem(props.item)"
                                        >
                                            <v-icon dark>delete</v-icon>
                                        </v-btn>
                                        <span>Eliminar</span>
                                    </v-tooltip>
                                </td>
                            </template>

                            <template slot="footer">
                                <td colspan="100%" v-if="loading" class="text-center">
                                    <v-progress-circular
                                        indeterminate
                                        color="primary"
                                    ></v-progress-circular>
                                </td>
                                <td colspan="100%" v-if="!loading">
                                    <v-container fluid>
                                        <v-layout row wrap>
                                            <v-flex xs1>
                                                <v-select
                                                    :items="['5', '10', '15', '25']"
                                                    label="Registros"
                                                    v-model="pagination.per_page"
                                                    @change="getData()"
                                                ></v-select>
                                            </v-flex>
                                            <v-flex xs11 class="text-right">
                                                <v-pagination
                                                    v-model="page"
                                                    total-visible="10"
                                                    :value="page"
                                                    @input="getData()"
                                                    :length="pagination.last_page"
                                                ></v-pagination>
                                            </v-flex>
                                        </v-layout>
                                    </v-container>
                                </td>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>

        <User
			:data="payload"
			:dialog="dialog"
			@close="closeDialog()"
			@process="processDialog()"
	  	/>
    </v-container>
</template>

<script>
import toastr from "toastr";
import swal from "sweetalert2";
    import User from './User.vue'
    
    export default {
        components: {
            User,
        },

        mounted() {
		    this.getData();
        },

        data(){
            return {
                headers: [
                    { text: 'Nombre', value: 'name' },
                    { text: 'Email', value: 'email' },
                    { text: 'Acciones', value: 'id',sortable: false, }
                ],
                items: [],
                loading: true,

                pagination: {
                    total: 0,
                    current_page: 0,
                    per_page: 5,
                    last_page: 0,
                    from: 0,
                    to: 0
                },
                search: "",
                page: 1,

                dialog: false,
                payload: {
                    url: "/users",
                    type: "add",
                    title: "Nuevo Usuario",
                    id: null,
                    name: null,
                    email: null,
                    password: null,
                    password_confirmation: null,
                }
            }
        },

        methods: {

            getData() {
                this.loading = true
                axios
                    .get(
                        `/users?page=${
                            this.page
                        }&per_page=${this.pagination.per_page}&search=${
                            this.search
                        }`
                    )
                    .then(response => {
                        this.loading = false;
                        this.pagination = response.data.pagination;
					    this.items = response.data.data.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },

            openDialog(payload = null) {
                if (payload) {
                    this.payload.url = `/users/${payload.id}`;
                    this.payload.type = "update";
                    this.payload.title = "Actualizar Usuario";
                    this.payload.id = payload.id;
                    this.payload.name = payload.name;
                    this.payload.email = payload.email;
                }
                this.dialog = true;
            },

            closeDialog() {
                this.dialog = false;
                this.payload.name = ''
                this.payload.email = ''
                this.payload.url = '/users'
                this.payload.id = ''
                this.payload.password = ''
                this.payload.password_confirmation = ''
            },

            processDialog(){
                this.dialog = false;
                this.getData()
                this.payload.name = ''
                this.payload.email = ''
                this.payload.url = '/users'
                this.payload.id = ''
                this.payload.password = ''
                this.payload.password_confirmation = ''
            },

            deleteItem(payload) {
                swal({
                    title: "¿Estás seguro?",
                    text:
                        "Una vez que se elimine, ¡no podrá recuperar este registro!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "OK!"
                }).then(result => {
                    if (result.value) {
                        axios
                            .delete(`/users/${payload.id}`)
                            .then(response => {
                                swal("Listo!", response.data[0], "success");
                                this.getData();
                            })
                            .catch(error => {
                                this.show = true;
                                var data = error.response.data.errors;
                                for (var i in data) {
                                    for (var j in data[i]) {
                                        toastr.warning(data[i][j]);
                                    }
                                }
                            });
                    }
                });
            }
        }
    }
</script>
