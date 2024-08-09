<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm, usePage} from '@inertiajs/vue3';
import {Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow} from '@/Components/ui/table'
import {Task} from "@/types";
import {Badge} from "@/Components/ui/badge";
import moment from 'moment';
import {DropdownMenu,DropdownMenuTrigger,DropdownMenuContent,DropdownMenuItem,DropdownMenuLabel,DropdownMenuSeparator, DropdownMenuCheckboxItem} from "@/Components/ui/dropdown-menu";
import {ListFilter,Search} from "lucide-vue-next";
import {Button} from "@/Components/ui/button";
import { router } from '@inertiajs/vue3'
import {ref, watch} from "vue";
import type { DropdownMenuCheckboxItemProps } from 'radix-vue'
import {Input} from "@/Components/ui/input";


let props = defineProps<{
    tasks?: Task[];
    currentView: string | null;
    searchString: string | undefined;
}>();

let searchString = ref(props.searchString);

const form = useForm({
    taskName: ""
});
type Checked = DropdownMenuCheckboxItemProps['checked']

const allChecked = ref<Checked>(props.currentView == 'all' || props.currentView == null)
const completedChecked = ref<Checked>(props.currentView == 'completed')
const inProgressChecked = ref<Checked>(props.currentView == 'inProgress')



watch(() => props.currentView, (value) => {
    allChecked.value = (value === 'all' || value === null);
    completedChecked.value = value === 'completed';
    inProgressChecked.value = value === 'inProgress';
});

watch(searchString, (value) =>{
    loadData(usePage().props.currentView as string, value);
})

function loadData(view: string, searchString: string | null = ''){
    router.get('/tasks',
        {
            view: view,
            searchString: searchString
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
}

</script>



<template>
    <Head title="Tasks" />

    <AuthenticatedLayout>

        <template #header>
            <h1>Tasks</h1>
        </template>

        <div class="ml-auto flex items-center gap-2">

            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" size="sm" class="h-7 gap-1">
                        <ListFilter class="h-3.5 w-3.5" />
                        <span class="sr-only sm:not-sr-only sm:whitespace-nowrap">
                      Filter
                    </span>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuLabel>Filter by</DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    <DropdownMenuCheckboxItem v-model:checked="allChecked" @click="loadData('all', searchString)" >
                        All
                    </DropdownMenuCheckboxItem>
                    <DropdownMenuCheckboxItem v-model:checked="completedChecked" @click="loadData('completed', searchString)">
                        Completed
                    </DropdownMenuCheckboxItem>
                    <DropdownMenuCheckboxItem v-model:checked="inProgressChecked" @click="loadData('inProgress', searchString)">In Progress</DropdownMenuCheckboxItem>
                </DropdownMenuContent>
            </DropdownMenu>

            <div class="relative ml-auto flex-1 md:grow-0">
                <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input
                    type="search"
                    v-model="searchString"
                    placeholder="Search..."
                    class="w-full rounded-lg bg-background pl-8 md:w-[200px] lg:w-[320px]"
                />
            </div>

        </div>

        <Table>
            <TableCaption>All your tasks.</TableCaption>
            <TableHeader>
                <TableRow>
                    <TableHead>Task</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead class="text-right">
                        Completed On
                    </TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="task in tasks" :key="task.id">
                    <TableCell class="font-medium">
                        {{ task.name }}
                    </TableCell>
                    <TableCell>
                        <Badge v-if="task.completed" variant="default">Completed</Badge>
                        <Badge v-else variant="outline">In Progress</Badge>
                    </TableCell>
                    <TableCell class="text-right">
                        {{ task.completed ? moment(task.completed_at).format('MMMM Do YYYY, h:mm:ss a') : '' }}
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>

    </AuthenticatedLayout>
</template>
