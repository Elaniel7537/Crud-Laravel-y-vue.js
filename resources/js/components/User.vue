<template>
	<v-dialog
      	v-model="dialog"
      	width="500"
      	persistent
    >
     	<v-card>
	        <v-card-title class="headline primary text-white" primary-title>
	          	{{data.title}}
	        </v-card-title>

	        <v-card-text>
	          	<v-form>
				    <v-text-field
				      	v-validate="'required'"
				      	v-model="data.name"
				      	:error-messages="errors.collect('Nombre')"
				      	label="Nombre"
				      	data-vv-name="Nombre"
				      	required
				    ></v-text-field>
				    <v-text-field
				      	v-model="data.email"
				      	v-validate="'required|email'"
				      	:error-messages="errors.collect('Email')"
				      	label="Email"
				      	data-vv-name="Email"
				    ></v-text-field>
                    <v-content v-if="data.type == 'add'">
                        <v-text-field
                            v-model="data.password"
                            v-validate="'required|min:6'"
                            :error-messages="errors.collect('Clave')"
                            label="Clave"
                            type="password"
                            data-vv-name="Clave"
                        ></v-text-field>
                        <v-text-field
                            v-model="data.password_confirmation"
                            v-validate="'required|min:6'"
                            :error-messages="errors.collect('Confirnmar Clave')"
                            label="Confirmar Clave"
                            type="password"
                            data-vv-name="Confirmar Clave"
                        ></v-text-field>
                    </v-content>

                    <v-content v-if="data.type == 'update'">
                        <v-text-field
                            v-model="data.password"
                            v-validate="'min:6'"
                            :error-messages="errors.collect('Clave')"
                            label="Clave"
                            type="password"
                            data-vv-name="Clave"
                        ></v-text-field>
                        <v-text-field
                            v-model="data.password_confirmation"
                            v-validate="'min:6'"
                            :error-messages="errors.collect('Confirnmar Clave')"
                            label="Confirmar Clave"
                            type="password"
                            data-vv-name="Confirmar Clave"
                        ></v-text-field>
                    </v-content>
			  	</v-form>
	        </v-card-text>

	        <v-divider></v-divider>

	        <v-card-actions>
	          	<v-spacer></v-spacer>
	          	<v-btn
		            color="aux"
		            flat
		            @click="close()"
					:loading="loading"
	          	>
	            	Cerrar
	          	</v-btn>
	          	<v-btn
		            color="primary"
		            flat
		            @click="process()"
					:loading="loading"
		            :disabled="disable || loading"
	          	>
	            	Aceptar
	          	</v-btn>
	        </v-card-actions>
      	</v-card>
    </v-dialog>
</template>

<script>
	import toastr from "toastr";
export default {

  	name: 'User',
  	props: ['data', 'dialog'],
  	data () {
	    return {
			loading: false
	    }
  	},
  	methods: {
  		process(){
			  this.$validator.validateAll().then(result => {
				  if (result) {
					this.loading = true
					if (this.data.type == "add") {
						axios
							.post(this.data.url, {
								name: this.data.name,
                                email: this.data.email,
                                password: this.data.password,
                                password_confirmation: this.data.password_confirmation
							})
							.then(response => {
								this.loading = false
								toastr.success(response.data);
								this.$validator.reset();
  								this.$emit('process')
							})
							.catch(error => {
                                var data = error.response.data.errors;
                                
                                if(error.response.data.error){
                                    toastr.warning(error.response.data.error)
                                }

								for (var i in data) {
									for (var j in data[i]) {
										toastr.warning(data[i][j]);
									}
								}
							});
					}
					if (this.data.type == "update") {
						axios
							.put(this.data.url, {
								id: this.data.id,
								name: this.data.name,
                                email: this.data.email,
                                password: this.data.password,
                                password_confirmation: this.data.password_confirmation
							})
							.then(response => {
								toastr.success(response.data);
								this.$validator.reset();
  								this.$emit('process')
							})
							.catch(error => {
                                var data = error.response.data.errors;
                                
                                if(error.response.data.error){
                                    toastr.warning(error.response.data.error)
                                }
                                
								for (var i in data) {
									for (var j in data[i]) {
										toastr.warning(data[i][j]);
									}
								}
							});
					}
				} else {
					toastr.warning("Existen errores en los campos");
				}
			});
          },
          
  		close(){
  			this.$validator.reset();
  			this.$emit('close')
  		}
  	},
  	computed: {
		disable() {
			return this.errors.all().length <= 0 ? false : true;
		}
	}
}
</script>

<style lang="css" scoped>
</style>
