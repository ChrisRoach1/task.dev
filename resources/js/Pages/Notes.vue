<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, usePage} from '@inertiajs/vue3';
import {Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow} from '@/Components/ui/table'
import {Note, Task} from "@/types";
import moment from 'moment';
import {Search} from "lucide-vue-next";
import {Button} from "@/Components/ui/button";
import { router } from '@inertiajs/vue3'
import {ref, watch} from "vue";
import {Input} from "@/Components/ui/input";
import {Popover, PopoverTrigger, PopoverContent} from "@/Components/ui/popover";
import {Calendar} from "@/Components/ui/calendar";
import {
    DateFormatter,
    type DateValue,
    CalendarDate,
    getLocalTimeZone,
} from '@internationalized/date'
import { CalendarIcon } from '@radix-icons/vue'
import { cn } from '@/lib/utils'
import { toDate, parseStringToDateValue } from 'radix-vue/date'
import Modal from "@/Components/Modal.vue";
import {Label} from "@/Components/ui/label";
import InputError from "@/Components/InputError.vue";

let props = defineProps<{
    notes?: Note[];
    dateFilter: string | null;
    searchString: string | undefined;
}>();


const todaysDate = ref<DateValue>();

let searchString = ref(props.searchString);


const df = new DateFormatter('en-US', {
    dateStyle: 'short',
});


const dateValue = ref<DateValue>();
const showModal = ref(false);
const currentlyViewedNote = ref<Note>(null);

watch(searchString, (value) =>{
    loadData(dateValue.value, value);
});

watch(dateValue, (value) =>{
    loadData(value, searchString.value);
})

const closeModal = () => {
    showModal.value = false;
};

function viewNote(note: Note){
    currentlyViewedNote.value = note;
    showModal.value = true;
}

function loadData(dateFilter: DateValue | undefined, searchString: string | null = ''){

    if(dateFilter)
        console.log(parseStringToDateValue('2024-08-04', dateFilter));


    router.get('/notes',
        {
            dateFilter: dateFilter ? moment(toDate(dateFilter)).format('YYYY-MM-DD') : null,
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
            <Popover>
                <PopoverTrigger as-child>
                    <Button  variant="outline" :class="cn('w-[280px] justify-start text-left font-normal',!dateValue && 'text-muted-foreground')">
                        <CalendarIcon class="mr-2 h-4 w-4" />
                        {{ dateValue ? df.format(dateValue.toDate(getLocalTimeZone())) : "Pick a date" }}
                    </Button>
                </PopoverTrigger>
                <PopoverContent class="w-auto p-0">
                    <Calendar v-model="dateValue" initial-focus />
                </PopoverContent>
            </Popover>

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
            <TableCaption>All your Notes.</TableCaption>
            <TableHeader>
                <TableRow>
                    <TableHead class="text-left">
                        Date
                    </TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="note in notes" :key="note.id">
                    <TableCell class="text-left" @click="viewNote(note)">
                        {{ moment(note.created_at).format('MMMM Do YYYY')  }}
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>

        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                {{currentlyViewedNote.content}}
            </div>
        </Modal>

    </AuthenticatedLayout>




</template>
