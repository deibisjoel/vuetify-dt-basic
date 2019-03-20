<template>
  <v-container>
	<v-card>
		<v-card-title class="font-weight-bold">
		Lista de Usuarios
		<v-spacer></v-spacer>
		<v-text-field
			v-model="search"
			append-icon="fa-search"
			label="	Ingrese término a buscar"
			single-line
		></v-text-field>
		<v-spacer></v-spacer>
		<v-dialog v-model="dialog" max-width="700px">
			<v-btn slot="activator" color="primary" dark class="mb-2">Nuevo Usuario</v-btn>
			<v-card >
				<v-card-title color="green" >
					<span class="headline">{{ formTitle }}</span>
				</v-card-title>
				<v-card-text>
					<v-container grid-list-md>
					<v-layout wrap>
						<v-flex xs12 sm6 md6>
						<v-text-field v-model="editedItem.name" label="Nombre Completo"></v-text-field>
						</v-flex>
						<v-flex xs12 sm6 md6>
						<v-text-field v-model="editedItem.email" label="Email"></v-text-field>
						</v-flex>
					</v-layout>
					</v-container>
				</v-card-text>

				<v-card-actions>
					<v-spacer></v-spacer>
					<v-btn color="blue darken-1" flat @click="close">Cancelar</v-btn>
					<v-btn color="green darken-1"   flat @click="save">Guardar</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
		</v-card-title>
			<v-data-table
				:headers="headers"
				:items="users"
				:pagination.sync="pagination"
				:total-items="pagination.totalItems"
				:loading="loading"
				:rows-per-page-items="rowsPerPageItems"
				:rows-per-page-text="rowsPerPageItemsText"
				@update:pagination="updatePagination"
				class="elevation-5"
			>
				<template slot="items" slot-scope="props">
					<td>{{ props.item.id }}</td>
					<td class="">{{ props.item.name }}</td>
					<td class="">{{ props.item.email }}</td>
					<td class="">{{ props.item.created_at }}</td>
					<td class="justify-center layout px-2">
						<v-icon
							color="success"
							md
							class="mr-2"
							@click="editItem(props.item)"
						>
							fa-edit
						</v-icon>
						<v-icon
							color="error"
							md
							@click="deleteItem(props.item)"
						>
							fa-trash
						</v-icon>
					</td>
				</template>
				<v-alert slot="no-results" :value="true" color="error" icon="warning">
					Su búsqueda para "{{ search }}" no obtuvo resultados.
				</v-alert>
				<v-alert slot="no-data" :value="true" color="error" icon="warning">
					No se encontraron datos para esta tabla
				</v-alert>
			</v-data-table>
	</v-card>

  </v-container>
</template>

<script>
  export default {
	name: 'dt-users',
    data: () => ({
		dialog: false,
		search: '',
		users: [],
		loading: true,
		pagination: {},
			headers: [
          {
            text: 'Id',
            align: 'left',
            value: 'id'
          },
          { text: '  Nombre  ', value: 'name' },
          { text: '  Email    ', value: 'email' },
		  { text: 'F. Creación', value: 'created_at' },
		  { text: 'Acciones', value: '' ,sortable: false },
		],
		rowsPerPageItems : [10,25,{"text":"Mostrar Todos","value":-1}],
		rowsPerPageItemsText: 'Filas por Página',

		editedIndex: -1,
		editedItem: {
			name: '',
			email: '',
		},
		data : []
    }),
	watch: {	
		search(){
			this.getDataFromApi();	
		},
		dialog (val) {
			val || this.close()
		}

    },
    methods: {
		updatePagination(){
			this.getDataFromApi();
		},
		getDataFromApi () {
			this.loading = true;
			axios.get('/api/users?page='+ this.pagination.page +'&per_page='+
			this.pagination.rowsPerPage+ '&sortBy='+ this.sort 
			+ '&order='+ this.order + '&search='+ this.search)
			.then((response)=>{
				this.users = response.data.data;
				this.pagination.page = response.data.current_page;
				this.pagination.rowsPerPage = response.data.per_page;
				this.pagination.totalItems = response.data.total; 
				this.loading = false;
			})
		},
		editItem (item) {
        	this.editedIndex = this.users.indexOf(item)
			this.editedItem = Object.assign({}, item)
			this.dialog = true
		},
		deleteItem (item) {
			if(confirm('Está seguro de desea eliminar el registro?')){
				axios.delete('/api/users/'+item.id + '/delete')
				.then((response)=>{
				});
			}
			this.getDataFromApi();
		},
		close () {
			this.dialog = false
			this.$nextTick(()=>{
				this.editedItem = Object.assign({}, this.defaultItem)
				this.editedIndex = -1
			})
		},
		save () {
			if (this.editedIndex > -1) {
				axios.put('/api/users/'+ this.editedItem.id + '/update',{
					data : this.editedItem
				})
				.then((response)=>{
					this.getDataFromApi();
					this.close();
					alert('Registro Actualizado');
				});
			} else {
				axios.post('/api/users/store',{
					data : this.editedItem
				})
				.then((response)=>{
					this.getDataFromApi();
					this.close();
					alert('Registro Guardado');
				});
			}
		}
	},
	computed: {
		order(){
			return (!this.pagination.descending) ? 'asc' : 'desc';
		},
		sort(){
			return this.pagination.sortBy ? this.pagination.sortBy : 'id';
		},
		formTitle () {
			return this.editedIndex === -1 ? 'Nuevo Usuario' : 'Editar Usuario'
		}
		
	},
  }
</script>

<style  >
.vdialognew {
  box-shadow: none !important;
  max-width: 610px !important;
}
</style>
