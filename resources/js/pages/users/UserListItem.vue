<script setup>

import {ref} from 'vue';
import {useToastr} from '../../toastr.js';

defineProps({
    user: Object,
    index: Number,
})

const toastr = useToastr();
const userIdBeingDeleted = ref(null);
const emit = defineEmits(['userDeleted', 'editUser']);

const confirmUserDeletion = (user) =>{
    userIdBeingDeleted.value = user.id;
    $('#deleteUserModal').modal('show');
}
const deleteUser = ()=>{
    axios.delete(`/api/users/${userIdBeingDeleted.value}`)
    .then(()=>{
        $('#deleteUserModal').modal('hide');
        toastr.success('用户删除成功!');
        emit('userDeleted', userIdBeingDeleted.value);
        // console.log(userIdBeingDeleted.value);
    });
}

// const editUser = (user) =>{
//     emit('editUser', user);
// }

const roles = ref([
    {
        name: 'ADMIN',
        value: 1,
    },
    {
        name: 'USER',
        value: 2,
    }
])
const changeRole = (user, role)=>{
    axios.patch(`/api/users/${user.id}/change-role`,{
        role: role,
    })
    .then(()=>{
        toastr.success('角色权限更改成功!')
    })
}


</script>

<template>
    <tr>
        <td>{{ index+1 }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ user.created_at ? user.created_at : '-' }}</td>
        <td>
            <select class="form-control" @change="changeRole(user, $event.target.value)" >
                <option v-for="role in roles" :value="role.value" :selected="(user.role === role.name)">{{ role.name }}</option>
            </select>
        </td>
        <td>
            <a href="#" @click.prevent="$emit('editUser', user)"><i class="fa fa-edit"></i> </a>
            &nbsp;
            <a href="#" @click.prevent="confirmUserDeletion(user)"><i class="fa fa-trash text-danger ml-2"></i> </a>
        </td>
    </tr>

    <div class="modal fade" id="deleteUserModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>删除用户</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <h5>确定删除此用户吗?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                    <button @click="deleteUser" type="button" class="btn btn-primary">确定</button>
                </div>
            </div>
        </div>
    </div>
</template>
