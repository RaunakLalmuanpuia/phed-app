
<template>
    <q-card class="q-mt-md">
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Transfer History</div>
            </div>

            <div class="flex q-gutter-sm">
                <q-btn v-if="canCreateTransfer" @click="showDialog = true" color="btn-primary" icon="add" />
            </div>
        </div>

        <q-card-section>
            <div v-if="sortedTransfers.length > 0">
                <q-timeline color="primary" class="q-pa-md">
                    <q-timeline-entry
                        v-for="(transfer, index) in sortedTransfers"
                        :key="transfer.id"
                        :title="formatDate(transfer.transfer_date)"
                        :icon="index === sortedTransfers.length - 1 ? 'flag' : 'swap_horiz'"
                    >
                        <template v-slot:subtitle>
                            <div class="flex items-center justify-between">
                                <span>Transfer #{{ index + 1 }}</span>
                                <q-btn
                                    v-if="canDeleteTransfer"
                                    icon="delete"
                                    color="red"
                                    flat
                                    dense
                                    size="m"
                                    @click="handleDelete(transfer.id)"
                                    title="Delete Transfer"
                                />
                            </div>
                        </template>

                        <div>
                            From
                            <strong>{{ transfer.old_office?.name || 'Unknown Office' }}</strong>
                            to
                            <strong>{{ transfer.new_office?.name || 'Unknown Office' }}</strong>
                        </div>
                        <div v-if="transfer.supporting_document">
                            Transfer Order:
                            <q-btn
                                dense
                                flat
                                color="primary"
                                icon="visibility"
                                :href="`/storage/${transfer.supporting_document}`"
                                target="_blank"
                            />
                            <q-btn
                                dense
                                flat
                                color="primary"
                                icon="download"
                                :href="`/storage/${transfer.supporting_document}`"
                                target="_blank"
                                download
                            />
                        </div>
                    </q-timeline-entry>
                </q-timeline>
            </div>

            <div v-else class="text-grey-7 text-center q-pa-md italic">
                No transfer history found
            </div>
        </q-card-section>

    </q-card>


    <q-dialog v-model="showDialog">
        <q-card style="min-width: 400px">
            <q-card-section>
                <div class="stitle">Add Transfer</div>
            </q-card-section>

            <q-card-section class="q-gutter-md">
                <q-checkbox
                    v-model="isPresentTransfer"
                    label="Is Present Transfer?"
                    color="primary"
                />

                <div v-if="isPresentTransfer">
                    <q-input
                        v-model="data.office.name"
                        label="Old Office"
                        dense
                        outlined
                        readonly
                    />
                </div>
                <div v-else>
                    <q-select
                        v-model="form.old_office_id"
                        :options="office"
                        option-value="id"
                        option-label="name"
                        label="Old Office"
                        dense
                        clearable
                        outlined
                    />
                </div>

                <q-select
                    v-model="form.new_office_id"
                    :options="office"
                    option-value="id"
                    option-label="name"
                    label="New Office"
                    dense
                    clearable
                    outlined
                />

                <q-input
                    v-model="form.transfer_date"
                    type="date"
                    label="Transfer Date"
                    dense
                    clearable
                    outlined
                />

                <q-file
                    v-model="form.supporting_document"
                    label="Transfer Order"
                    outlined
                    clearable
                    :error="!!form.errors.supporting_document"
                    :error-message="form.errors.supporting_document"
                    accept=".pdf,.jpg,.jpeg,.png"
                />
            </q-card-section>

            <q-card-actions align="right">
                <q-btn flat label="Cancel" v-close-popup />
                <q-btn flat label="Submit" color="primary" @click="submit" />
            </q-card-actions>
        </q-card>
    </q-dialog>

</template>

<script setup>
import {computed, ref, watch} from "vue";
import { useQuasar } from 'quasar'
import {router, useForm} from "@inertiajs/vue3";
const props=defineProps(['data', 'office','canCreateTransfer','canDeleteTransfer']);
const $q = useQuasar()


const showDialog = ref(false)


const isPresentTransfer = ref(false)

const form=useForm({
    old_office_id: null,
    new_office_id: null,
    transfer_date: '',
    is_present_transfer:false,
    supporting_document: null,

})
const sortedTransfers = computed(() => {
    return [...(props.data?.transfers || [])].sort((a, b) =>
        new Date(a.transfer_date) - new Date(b.transfer_date)
    )
})

function formatDate(dateStr) {
    const date = new Date(dateStr)
    return date.toLocaleDateString('en-IN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const submit = (e) => {

    if (!form.old_office_id) {
        $q.notify({
            type: 'negative',
            message: 'Old Office is required',
            position: 'bottom',
        });
        return; // stop submission
    }

    if (!form.new_office_id) {
        $q.notify({
            type: 'negative',
            message: 'New Office is required',
            position: 'bottom',
        });
        return; // stop submission
    }

    if (!form.transfer_date) {
        $q.notify({
            type: 'negative',
            message: 'Transfer Date is required',
            position: 'bottom',
        });
        return; // stop submission
    }

    if (!form.supporting_document) {
        $q.notify({
            type: 'negative',
            message: 'Transfer Order is required',
            position: 'bottom',
        });
        return; // stop submission
    }


    form.is_present_transfer = isPresentTransfer.value // add this line
    form.post(route('transfer.store', props.data), {
        onStart: () => {
            $q.loading.show({
                boxClass: 'bg-grey-2 text-grey-9',
                spinnerColor: 'primary', message: 'Creating Transfer...'
            })
            form.reset();
        },
        onFinish: () => {
            $q.loading.hide()
            showDialog.value = false
            form.reset();
        },
        onError: (errors) => {
            $q.loading.hide()
            // Show all validation errors from backend
            Object.values(errors).forEach((error) => {
                $q.notify({
                    type: 'negative',
                    message: error,
                    position: 'bottom',
                })
            })
        }
    })
}

const handleDelete=item=>{
    $q.dialog({
        title:'Confirmation',
        message:'Do you want to delete?',
        ok:'Yes',
        cancel:'No'
    }).onOk(()=>{
            router.delete(route('transfer.destroy',item),{
                onStart:params => $q.loading.show(),
                onFinish:params => $q.loading.hide(),
                preserveState: false
            })
        })
}
watch(isPresentTransfer, (newVal) => {
    if (newVal) {
        form.old_office_id = props.data.office
        form.transfer_date = new Date().toISOString().split('T')[0]
    } else {
        form.old_office_id = null // or reset to something else if needed
        form.transfer_date=''
    }
})
</script>



<style scoped>

</style>
