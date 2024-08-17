<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm, usePage} from '@inertiajs/vue3';
import { Activity, ArrowUpRight, CreditCard, DollarSign, Users, CircleCheck } from 'lucide-vue-next'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import {Note, Task} from "@/types";
import {Input} from "@/Components/ui/input";
import InputError from "@/Components/InputError.vue";
import {CheckIcon} from "@radix-icons/vue";
import { Textarea } from '@/Components/ui/textarea'
import { debounce } from 'lodash-es';
import {ref} from "vue";

let props = defineProps<{
    tasks?: Task[];
    notes?: Note;
    tasksCompleted: number;
    totalUsers: number;
}>();


const form = useForm({
    taskName: ""
});

const tasksCompletedCount = ref(props.tasksCompleted);

const notesForm = useForm({
    id: props.notes?.id,
    content: props.notes?.content
});

const deleteForm = useForm({
    id: 0
});

const debounceNoteUpdate = debounce(() =>{
    notesForm.patch(route('note.update'));

}, 1000);

function deleteTask(taskID: number){
    deleteForm.id = taskID;

    deleteForm.delete(route('task.destroy'), {preserveScroll: true,replace: true, preserveState:false });
}

</script>



<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>

        <template #header>
            <h1>Dashboard</h1>
        </template>

        <div class="grid gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">
                        Total Tasks Completed Across All Users
                    </CardTitle>
                    <CircleCheck class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">
                        {{ tasksCompletedCount }}
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">
                        Total Users
                    </CardTitle>
                    <Users class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">
                        {{ props.totalUsers }}
                    </div>
                </CardContent>
            </Card>
        </div>
        <div class="grid gap-4 md:gap-8 lg:grid-cols-2 xl:grid-cols-3">
            <Card class="xl:col-span-2">
                <CardHeader class="flex flex-row items-center">
                    <div class="grid gap-2">
                        <CardTitle>Tasks</CardTitle>
                    </div>
                    <Button as-child size="sm" class="ml-auto gap-1">
                        <a :href="route('tasks')">
                            View All
                            <ArrowUpRight class="h-4 w-4" />
                        </a>
                    </Button>
                </CardHeader>

                <form @submit.prevent="form.post(route('task.create'), {onSuccess: () => form.reset('taskName'), preserveScroll: true})" class="flex flex-row ml-5 mb-5 space-y-2 space-x-3 items-center">
                    <div>
                        <Input
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.taskName"
                            required
                            autofocus
                            autocomplete="name"
                        />

                        <InputError class="mt-2" :message="form.errors.taskName" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save</Button>
                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                        </Transition>
                    </div>
                </form>

                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Task</TableHead>
                                <TableHead class="hidden xl:table-column">
                                    Type
                                </TableHead>
                                <TableHead class="float-end">
                                    Crush it
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody v-for="task in tasks">
                            <TableRow>
                                <TableCell>
                                    <div class="font-medium">
                                        {{ task.name }}
                                    </div>
                                </TableCell>
                                <TableCell class="float-end">
                                    <Button variant="ghost" @click="deleteTask(task.id)">
                                        <CheckIcon class="w-4 h-4" />
                                    </Button>

                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center">
                    <div class="flex flex-row space-x-3 items-center">
                        <CardTitle>Recent Notes</CardTitle>
                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0">
                            <p v-if="notesForm.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                        </Transition>
                    </div>
                    <Button as-child size="sm" class="ml-auto gap-1">
                        <a :href="route('notes')">
                            View All
                            <ArrowUpRight class="h-4 w-4" />
                        </a>
                    </Button>
                </CardHeader>
                <CardContent class="grid gap-8 ">
                    <Textarea
                        v-model="notesForm.content" @input="debounceNoteUpdate"
                        id="message"
                        placeholder="Type your notes here..."
                        class="min-h-12 border-0 p-3 shadow-none focus-visible:ring-0"
                    />
                </CardContent>
            </Card>
        </div>

    </AuthenticatedLayout>
</template>
