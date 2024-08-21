<template>
  <div class="h-auto w-full flex flex-col p-5 gap-10">
    <section>
      <UBreadcrumb :links="links">
        <template #divider>
          <UIcon name="i-lucide-chevron-right" class="text-lg" />
        </template>
        <template #default="{ link, isActive }">
          <div :class="{
            'dark:text-white text-custom-800 text-lg cursor-default': isActive,
            'text-custom-300 hover:text-custom-500 hover:dark:text-custom-300 dark:text-custom-500 text-lg': !isActive
          }" class="rounded-full">
            {{ link.label }}
          </div>
        </template>
      </UBreadcrumb>
    </section>
    <section class="h-4/5 w-full flex justify-center items-center">
      <div class="sm:w-3/4 w-full h-auto flex flex-col gap-5">
        <div class="flex justify-between">
          <div class="font-semibold cursor-default flex items-center gap-1 w-1/2">
            <UIcon name="i-lucide-book-open-text" class="text-xl" />
            <h1 class="font-bold text-xl">Notification Details</h1>
          </div>
          <div class="flex justify-end w-1/2 gap-x-2">
            <section class="w-auto">
              <UButton label="Back" icon="i-lucide-move-left"
                class="flex justify-center w-full items-center rounded dark:bg-red-600 dark:hover:bg-red-500 bg-red-700 hover:bg-red-600 dark:text-custom-100"
                to="/client/notifications" />
            </section>
          </div>
        </div>
        <div class="lg:flex grid gap-5 w-full">
          <section class="bg-custom-100 dark:bg-custom-900 border border-custom-300 dark:border-custom-700 rounded p-5 lg:w-1/2 w-full">
            <h1 class="font-semibold">Motion Detected</h1>
            <hr class="border-custom-200 dark:border-custom-700 mt-2 mb-2">
            <section class="my-auto">
              <div v-for="(m, index) in motion" :key="index" class="grid grid-cols-3 gap-5 my-2">
                <h1 class="capitalize col-span-1">{{ m.label }}:</h1>
                <div class="col-span-2 dark:text-custom-300 text-custom-500 capitalize">
                  <template v-if="m.label === 'level'">
                    <UKbd :class="{
          ' bg-green-600 px-2 dark:border dark:border-green-700 text-white dark:text-green-400 cursor-default': state.level === 'normal',
          ' bg-yellow-500 px-2 dark:border dark:border-yellow-500 text-white dark:text-yellow-400 cursor-default': state.level === 'warning',
          'bg-red-600 px-2 dark:border dark:border-red-700 text-white dark:text-red-400 cursor-default': state.level === 'danger',
                    }" :value="state.level" />
                  </template>
                  <p v-else>{{ m.value }}</p>
                </div>
              </div>
            </section>
          </section>
          <div class="flex flex-col gap-5 lg:w-1/2 w-full">
            <UForm class="h-auto w-full flex flex-col gap-3" :state="state" @submit="onSubmit" :validate="validate" @error="onError">
              <section class="bg-custom-100 dark:bg-custom-900 rounded p-5 border border-custom-300 dark:border-custom-700">
                <div class="flex justify-between items-center">
                  <h1 class="font-semibold">Status</h1>
                  <section class="w-auto">
                    <UButton :label="label" :loading-icon="loadIcon" :loading="loading" icon="i-lucide-save"
                      class="flex justify-center w-full items-center rounded dark:text-white" type="submit" size="xs" />
                  </section>
                </div>
                <hr class="border-custom-200 dark:border-custom-700 mt-2 mb-2">
                <section class="my-auto">
                  <UFormGroup class="w-2/3" name="status">
                    <URadioGroup v-model="state.status" :options="statusOptions" class="ml-2"
                      :uiRadio="{ color: state.status === statusOptions[0].value ? 'text-green-500' : 'text-orange-500' }" />
                  </UFormGroup>
                </section>
              </section>
            </UForm>
            <section class="bg-custom-100 dark:bg-custom-900 rounded p-5 border border-custom-300 dark:border-custom-700">
              <h1 class="font-semibold">Timestamps (date & time)</h1>
              <hr class="border-custom-200 dark:border-custom-700 mt-2 mb-2">
              <section class="my-auto">
                <div v-for="(t, index) in timestamp" :key="index" class="grid grid-cols-5 gap-5 my-2">
                  <h1 class="capitalize col-span-2">{{ t.label }}:</h1>
                  <p class="col-span-3 dark:text-custom-300 text-custom-500 capitalize">{{ t.value }}</p>
                </div>
              </section>
            </section>
          </div>
        </div>
        <!-- Image Display Section -->
        <div class="flex justify-center items-center h-full w-full bg-white border border-custom-300 dark:border-custom-700">
          <!-- Bind image source dynamically -->
          <img class="w-auto lg:h-[450px] h-auto" :src="imageSrc" alt="Snapshot Image">
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import type { FormErrorEvent, FormSubmitEvent } from '#ui/types';

definePageMeta({
  layout: 'sidebar'
})

const state = reactive({
  status: 'approved',
  notification_id: undefined,
  date_captured: undefined,
  motion_detected: undefined,
  level: undefined,
  user_id: undefined,
  snapshot: undefined,
  created_at: undefined,
  updated_at: undefined
});

const motion = [
  { label: 'name', value: '' },
  { label: 'percentage', value: '60%' },
  { label: 'level', value: '' }  // Update the default value here
];

const timestamp = [
  { label: 'date captured', value: '' },
  { label: 'last update', value: '' }
];

const statusOptions = [
  { value: 'approved', label: 'Approved' },
  { value: 'ignored', label: 'Ignored' }
];

const route = useRoute();
const id = ref(route.params.id); // Assuming id is from route params

const imageSrc = ref<string | undefined>(''); // Reactive ref for image source

const fetchNotification = async () => {
  try {
    const response = await fetch(`http://127.0.0.1:8000/api/notifications/showSpecific/${id.value}`);
    const data = await response.json();

    if (data.notifications.length > 0) {
      const notification = data.notifications[0];
      state.notification_id = notification.notification_id;
      state.date_captured = notification.date_captured;
      state.motion_detected = notification.motion_detected;
      state.level = notification.level;
      state.status = notification.status;
      state.user_id = notification.user_id;
      state.created_at = notification.created_at;
      state.updated_at = notification.updated_at;
      state.snapshot = notification.snapshot;

      // Construct image source dynamically
      imageSrc.value = `http://127.0.0.1:8000/snapshots/${notification.snapshot}`;
      
      // Update motion and timestamp values
      motion[0].value = notification.motion_detected;
      motion[2].value = notification.level;  // Update the level value here
      timestamp[0].value = notification.date_captured;
      timestamp[1].value = notification.updated_at;
    }
  } catch (error) {
    console.error('Error fetching notification data:', error);
  }
};

onMounted(() => {
  fetchNotification();
});

const loading = ref(false);
const loadIcon = ref('');
const label = ref('Update');

async function onSubmit(event: FormSubmitEvent<any>) {
  loading.value = true;
  loadIcon.value = 'i-lucide-loader-circle';
  label.value = '';

  try {
    const response = await fetch(`http://127.0.0.1:8000/api/notifications/${state.notification_id}/edit`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        status: state.status,
        // Include any other fields you need to update
      }),
    });

    if (response.ok) {
      // Assuming successful update logic here
      console.log('Notification status updated successfully');
      label.value = 'Update';
      loading.value = false;
      navigateTo('/client/notifications');
    } else {
      throw new Error('Failed to update notification status');
    }
  } catch (error) {
    console.error('Error updating notification status:', error);
    // Handle error as needed
    label.value = 'Update';
    loading.value = false;
  }
}

async function onError(event: FormErrorEvent) {
  const element = document.getElementById(event.errors[0].id);
  element?.focus();
  element?.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
</script>
