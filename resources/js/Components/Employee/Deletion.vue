<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import {useQuasar} from "quasar";
const $q = useQuasar()


const props=defineProps(['deletion','canEditDelete']);
const showDialog = ref(false)

const form = useForm({
  reason: props.deletion.reason || '',
  seniority_list: props.deletion.seniority_list || '',
  year: props.deletion.year || new Date().getFullYear(),
  remark: props.deletion.remark || '',
  supporting_document: null,
})

const submit = () => {
  form.post(route('deletion.update', props.deletion), {
    preserveScroll: true,
    onSuccess: () => {
      showDialog.value = false
        $q.notify({
            type: 'positive',
            message:  'Deleted details updated successfully.'
        })
    }
  })
}

const viewDocument = (url) => {
  window.open(url, '_blank')
}

</script>

<template>

  <q-card class="q-mt-md">
    <div class="flex items-center justify-between q-pa-md bg-white">
      <div>
        <div class="stitle text-red">Deletion Detail</div>
      </div>

      <div class="flex q-gutter-sm">
        <q-btn v-if="canEditDelete" @click="showDialog = true" color="btn-primary" icon="edit" />
      </div>
    </div>

    <q-card-section>

      <q-list class="q-mt-sm">

        <q-item>
          <q-item-section side class="subtitle">Reason:</q-item-section>
          <q-item-section  class="text-label">{{ deletion.reason }}</q-item-section>
        </q-item>

        <q-item>
          <q-item-section side class="subtitle">Seniority List:</q-item-section>
          <q-item-section  class="text-label">{{ deletion.seniority_list }}</q-item-section>
        </q-item>

        <q-item>
          <q-item-section side class="subtitle">Year:</q-item-section>
          <q-item-section  class="text-label">{{ deletion.year }}</q-item-section>
        </q-item>

        <q-item v-if="deletion.remark">
          <q-item-section side class="subtitle">Remark:</q-item-section>
          <q-item-section  class="text-label">{{ deletion.remark }}</q-item-section>
        </q-item>


        <q-item v-if="deletion.supporting_document">
          <q-item-section side class="subtitle">Supporting Document:</q-item-section>
          <q-item-section  class="text-label"><q-btn
              dense
              flat
              icon="visibility"
              :href="`/storage/${deletion.supporting_document}`"
              target="_blank"
          />
          </q-item-section>
        </q-item>

      </q-list>
    </q-card-section>
  </q-card>

  <q-dialog v-model="showDialog">
    <q-card style="min-width: 500px; max-width: 90vw;">
      <q-card-section class="bg-negative text-white">
        <div class="text-h6">Edit Deletion Detail</div>
      </q-card-section>

      <q-form @submit.prevent="submit">
        <q-card-section class="q-gutter-md">
            <q-select
                v-model="form.reason"
                label="Reason"
                outlined
                dense
                required
                :options="[
                        'Expired',
                        'Resigned',
                        'Dismissed',
                        'Regularised',
                        'Others (Specify the reasons to Remarks)',
                        'Overage (Retired)'
                      ]"
                :error="!!form.errors.reason"
                :error-message="form.errors.reason"
                :rules="[
                        val => !!val || 'Reason is required'
                     ]"
            />
          <q-input v-model="form.seniority_list" label="Seniority List" outlined dense />
          <q-input v-model="form.year" label="Year" outlined dense type="number" />
          <q-input v-model="form.remark" label="Remark" outlined dense type="textarea" />

          <q-file
              v-model="form.supporting_document"
              label="Supporting Document"
              outlined
              dense
              accept=".pdf,.png,.jpg,.jpeg"
          >
            <template v-if="deletion.supporting_document" v-slot:append>
              <q-btn
                  round
                  dense
                  flat
                  icon="visibility"
                  :href="`/storage/${deletion.supporting_document}`"
                  @click="viewDocument(`/storage/${deletion.supporting_document}`)"
                  class="q-ml-sm"
              />
            </template>
          </q-file>

        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn color="primary" label="Update" type="submit" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>


</template>

<style scoped>


</style>
