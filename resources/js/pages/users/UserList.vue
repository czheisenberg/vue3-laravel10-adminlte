<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">用户</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">主页</a></li>
                        <li class="breadcrumb-item active">用户</li>
                    </ol>
                </div>
            </div>
        </div>

        </div>
           <div class="content">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between">
                        <button @click="addUser" type="button" class="mb-2 btn btn-primary" >
                    
                            添加用户

                        </button>
                        <div>
                            <input type="text" v-model="searchQuery" class="form-control" placeholder="搜索..." />
                            <button @click.prevent="search">提交</button>
                        </div>      
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>姓名</th>
                                        <th>邮箱</th>
                                        <th>注册日期</th>
                                        <th>角色</th>
                                        <th>选项</th>
                                    </tr>
                                </thead>
                                <tbody v-if="users.length >0">
                                    <UserListItem v-for="(user, index) in users" 
                                    
                                        :key="user.id"
                                        :user=user
                                        :index="index" 
                                        @edit-user="editUser"
                                        @user-deleted = "userDeleted"
                                    />
                                    <!-- <tr v-for="(user, index) in users">
                                        <td>{{ index+1 }}</td>
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.email }}</td>
                                        <td>{{ user.created_at ? user.created_at : '-' }}</td>
                                        <td>{{ user.role}}</td>
                                        <td>
                                            <a href="#" @click.prevent="editUser(user)"><i class="fa fa-edit"></i> </a>
                                            &nbsp;
                                            <a href="#" @click.prevent="confirmUserDeletion(user)"><i class="fa fa-trash text-danger ml-2"></i> </a>
                                        </td>
                                    </tr> -->

                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="6" class="text-center">No results found...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        
    <div class="modal fade" id="userFormModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">修改用户</span>
                        <span v-else>添加用户</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editUserSchema: createUserSchema" v-slot="{ errors }" :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <Field name="name" type="text" class="form-control " :class="{'is-invalid': errors.name}"  id="name" aria-describedby="nameHelp" placeholder="Enter full name"  />
                            <span class="invalid-feedback" >{{ errors.name }}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <Field name="email" type="email" class="form-control "  :class="{'is-invalid': errors.email}"  id="email" aria-describedby="nameHelp" placeholder="Enter full name" />
                            <span class="invalid-feedback" >{{ errors.email }}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Password</label>
                            <Field name="password" type="password" class="form-control " :class="{'is-invalid': errors.password}"  id="password" aria-describedby="nameHelp" placeholder="Enter password" />
                            <span class="invalid-feedback" >{{ errors.password }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </Form>
            </div>
        </div>
    </div>


</template>

<script setup>
import axios from 'axios';
import {onMounted, ref,reactive, watch} from 'vue';
import {Form, Field} from 'vee-validate';
import * as yup from 'yup';
import {useToastr} from '../../toastr.js';
import UserListItem from './UserListItem.vue';
import {debounce} from 'lodash';

const toastr = useToastr();
const users = ref([]);
const editing = ref(false);
const formValues = ref();
const form = ref(null);


const getUsers = () =>{
    axios.get('/api/users/')
    .then((response)=>{
        users.value = response.data;
    })
}
onMounted(()=>{
    getUsers();
})


const createUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(8),
});


const editUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().when((password, schema) => {
        return password ? schema.required().min(8) : schema;
    }),
});


const createUser = (values, {resetForm, setErrors}) =>{
    // console.log(values);
    axios.post('/api/users/', values)
    .then((response) =>{
        users.value.push(response.data);    // push添加到最后, unshift则是最上面
        $('#userFormModal').modal('hide');
        resetForm();    // clear form text
        toastr.success('添加成功!');
    })
    .catch((error) =>{
        if(error.response.data.errors){
            setErrors(error.response.data.errors)
        }
    })
};

const addUser = () =>{
    editing.value = false;
    $('#userFormModal').modal('show');
}

const editUser = (user) =>{
    editing.value = true;
    form.value.resetForm();
    $('#userFormModal').modal('show');
    formValues.value = {
        id: user.id,
        name: user.name,
        email: user.email,
    };
    // console.log(user);
}
const updateUser = (values, {setErrors})=>{
    axios.put('/api/users/' + formValues.value.id, values)
        .then((response) => {
            const index = users.value.findIndex(user=>user.id === response.data.id);
            users.value[index] = response.data;
            $('#userFormModal').modal('hide');
            toastr.success('更新成功!');
        }).catch((error) => {
            setErrors(error.response.data.errors);
        });
}
const handleSubmit = (values, actions)=>{
    if(editing.value){
        updateUser(values, actions);
    }else{
        createUser(values, actions);
    }
}

const userDeleted = (userId)=>{
     users.value = users.value.filter(user=>user.id !== userId);
}


const searchQuery = ref(null)
watch(searchQuery,debounce(()=>{
    search()
},300))

const search = ()=>{
    axios.get('/api/users/search',{
        params:{
            query: searchQuery.value
        }
    })
    .then(response=>{
        users.value = response.data;
    })
    .catch(error =>{
        console.log(error)
    })
}


</script>