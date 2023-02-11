<template>
<Head title="Students" />
<PageHeader :title="title" :items="items" />
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">

        <div class="file-manager-sidebar">
            <div class="p-4 d-flex flex-column h-100">

                <div class="mt-4 mb-3 border-bottom pb-2">
                    <div class="float-end">
                        <a class="link-primary" href="javascript:void(0);" target="_self">All Logout</a>
                    </div>
                    <h5 class="card-title">Login History</h5>
                </div>
                <div class="d-flex align-items-center mb-3" v-for="(list, index) of logs" :key="index">
                    <div class="flex-shrink-0 avatar-sm">
                        <div class="avatar-title bg-light text-primary rounded-3 fs-18">
                            <i :class="'ri-'+list.type+'-line '+list.attempt"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">{{list.platform}} ({{list.browser}})</h6>
                        <p class="text-muted fs-11 mb-0">  {{ list.location.state_name }}, {{ list.location.country }} </p>
                        <p class="text-muted fs-11 mb-0" style="margin-top: -2px;"> {{ list.login_at}}</p>
                    </div>
                </div>
            </div>
        </div>
                    
            <div>   
        </div>
            
        <div class="file-manager-content w-100 p-4 pb-0" style="height: calc(100vh - 180px)">
            
            <div class="p-3 bg-light rounded mb-4">
                <b-row class="g-2">
                    <b-col lg>
                        <div class="search-box">
                            <input type="text" id="searchTaskList" class="form-control search"
                                placeholder="Search name">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </b-col>
                    <b-col lg="auto">
                        <b-button variant="primary" class="createTask" @click="add"> <i
                                class="ri-add-fill align-bottom"></i> New Staff
                        </b-button>
                    </b-col>
                </b-row>
            </div>

            <div class="todo-content position-relative px-4 mx-n4" id="todo-content">
                <div id="elmLoader" v-if="lists.length == 0">
                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                        colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px"></lord-icon>
                    <h5 class="mt-4">Sorry! No Result Found</h5>
                </div>
                <div class="todo-task" id="todo-task" v-else>
                    <div class="table-responsive">
                        <table class="table align-middle position-relative table-nowrap">
                            <thead class="text-muted bg-soft-light">
                                <tr>
                                    <th width="2%"></th>
                                    <th scope="col">Name</th>
                                    <th scope="col" class="text-center">Role</th>
                                    <th scope="col" class="text-center">Status</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>

                            <tbody id="task-lists">
                                <tr v-for="(list, index) of lists" :key="index">
                                    <td>
                                        <div class="avatar-xs">
                                            <span v-if="list.avatar == 'avatar.jpg'" :class="'avatar-title rounded-circle bg-'+list.g+' text-white'">{{list.lastname[0]}}</span>
                                            <img v-else :src="currentUrl+'/images/avatars/'+list.avatar" alt="" class="avatar-xs rounded-circle">
                                        </div>
                                        
                                    </td>
                                    <td class="fs-14 my-1 fw-medium">{{ list.name}}</td>
                                    <td class="text-center">{{ list.role }}</td>
                                    <td class="text-center">
                                        <span v-if="list.is_active == 1" class="badge bg-success fs-11">Active</span>
                                        <span v-else class="badge bg-danger fs-11">Inactive</span>
                                    </td>
                                    <td class="text-end">
                                        <Link :href="'students/'+list.id">
                                        <b-button variant="soft-danger" size="sm" class="remove-list me-1"><i class="ri-eye-fill align-bottom"></i></b-button>
                                        </Link>
                                        <b-button variant="primary" size="sm" class="edit-list"><i class="ri-pencil-fill align-bottom"></i> </b-button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="py-4 mt-4 text-center" id="noresult" style="display: none;">
                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                        colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px"></lord-icon>
                    <h5 class="mt-4">Sorry! No Result Found</h5>
                </div>
            </div>
        </div>
    </div>
<Add ref="new"/>
</template>
<script>
import Add from './Modals/Add.vue';
import Multiselect from '@suadelabs/vue3-multiselect';
import PageHeader from "@/Shared/Components/PageHeader.vue";
import flatPickr from "vue-flatpickr-component";
export default {
    components : { PageHeader, Multiselect, flatPickr, Add },
    page: {
        title: "List of Staffs",
        meta: [{ name: "description" }],
    },
    data() {
        return {
            currentUrl: window.location.origin,
            title: "List of Staffs",
            items: [{text: "Lists",href: "/",},{text: "Staff",active: true,},],
            logs: [],
            lists : [],
            meta: {},
            links: {},
        };
    },
    created(){
        this.fetch();
        this.fetchLogs();
    },
    methods : {
        add(){
            this.$refs.new.show();
        },

        fetch(page_url){
            page_url = page_url || '/staffs';
            axios.get(page_url,{
                params : {
                    keyword : this.keyword,
                    count: this.count,
                    search: true
                }
            })
            .then(response => {
                if(response){
                    this.lists = response.data.data;                    
                    this.meta = response.data.meta;
                    this.links = response.data.links;
                    this.page = this.meta.per_page*(this.meta.current_page - 1);
                }
            })
            .catch(err => console.log(err));
        },

        fetchLogs(){
            axios.get(this.currentUrl+'/staffs/logs')
            .then(response => {this.logs = response.data.data;})
            .catch(err => console.log(err));
        },
    }
}
</script>
<style>
.file-manager-sidebar {
  min-width: 500px;
  max-width: 500px;
  height: calc(100vh - 180px);
}
</style>
