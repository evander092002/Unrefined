<template>
  <section class="items-center grid gap-5">
    <div class="flex sm:gap-0 gap-5 sm:flex-row flex-col-reverse sm:justify-between justify-center lg:items-end">
      <div class="flex gap-1 justify-start items-center">
        <UInput v-model="q" name="q" placeholder="Search..." icon="i-heroicons-magnifying-glass-20-solid"
          autocomplete="off" color="gray" size="sm"
          :ui="{ rounded: 'rounded', color: { gray: { outline: 'dark:bg-custom-100 dark:text-custom-900' } }, icon: { trailing: { pointer: '' } } }"
          class="w-full sm:w-auto sm:-mb-0 -mb-5">
          <template #trailing>
            <UButton v-show="q !== ''" color="gray" variant="link" icon="i-heroicons-x-mark-20-solid" :padded="false"
              @click="q = ''"
              class="hover:text-red-400 dark:hover:text-red-600 text-red-700 dark:text-red-400" />
          </template>
        </UInput>
      </div>
      <div class="grid justify-center items-center gap-2">
        <div class="lg:flex justify-end hidden">
          <span class="text-xs leading-5">
            Showing
            <span class="font-medium">{{ startItem }}</span>
            -
            <span class="font-medium">{{ endItem }}</span>
            of
            <span class="font-medium">{{ totalNotifications }}</span>
            results
          </span>
        </div>
        <UPagination :model-value="currentPage" :page-count="pageCount" :total="totalNotifications" :ui="{
          color: 'gray',
          wrapper: 'flex items-center gap-1',
          rounded: '!rounded-full min-w-[30px] justify-center',
          default: {
            activeButton: {
              variant: 'outline'
            }
          }
        }" @update:model-value="updatePage" class="flex justify-center" />
      </div>
    </div>
    <UTable :columns="tableHeaders" :rows="paginatedData" sort-asc-icon="i-heroicons-arrow-up"
      sort-desc-icon="i-heroicons-arrow-down"
      class="max-h-[70vh] max-w-full overflow-auto border rounded border-custom-300 dark:border-custom-800"
      :ui="{ thead: 'sticky top-0 z-10 dark:bg-custom-700 bg-custom-300 cursor-default', tbody: 'bg-custom-100 dark:bg-custom-950 cursor-default' }">
      <template #loading-state>
        <div class="flex items-center justify-center h-32 gap-2">
          <UIcon name="i-lucide-loader-circle" class="animate-spin text-2xl" />
          <p>In a moment...</p>
        </div>
      </template>
      <template #id-data="{ index }">
        <span>
          {{ (currentPage - 1) * pageCount + index + 1 }}
        </span>
      </template>
      <template #level-data="{ row }">
        <UKbd :class="{
          ' bg-green-600 px-2 dark:border dark:border-green-700 text-white dark:text-green-400 cursor-default': row.level === 'normal',
          ' bg-yellow-500 px-2 dark:border dark:border-yellow-500 text-white dark:text-yellow-400 cursor-default': row.level === 'warning',
          'bg-red-600 px-2 dark:border dark:border-red-700 text-white dark:text-red-400 cursor-default': row.level === 'danger',
        }" :value="row.level" />
      </template>
      <template #status-data="{ row }">
        <div v-if="row.status === 'approved'" class="flex items-center gap-1 text-green-500">
          <UIcon name="i-lucide-badge-check" class="text-lg" />
          <p class="text-sm font-semibold">{{ row.status }}</p>
        </div>
        <div v-else-if="row.status === 'ignored'" class="flex gap-1 text-orange-500">
          <UIcon name="i-lucide-badge-minus" class="text-lg" />
          <p class="text-sm font-semibold">{{ row.status }}</p>
        </div>
      </template>
      <template #action-data="{ row }">
        <UTooltip text="View" :popper="{ arrow: true, placement: 'right' }"
          :ui="{ background: 'dark:bg-custom-800 bg-custom-50', arrow: { background: 'dark:before:bg-custom-700 before:bg-custom-300' } }">
          <UIcon name="i-lucide-eye" class="text-xl hover:opacity-75 text-blue-500" @click="viewActionModal(row)" />
        </UTooltip>
      </template>
    </UTable>
    <div class="flex justify-center lg:hidden">
      <span class="text-xs leading-5">
        Showing
        <span class="font-medium">{{ startItem }}</span>
        -
        <span class="font-medium">{{ endItem }}</span>
        of
        <span class="font-medium">{{ totalNotifications }}</span>
        results
      </span>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue';
import ModalViewNotifications from '#components'; // Adjust path as needed

const notifications = ref([]);
const currentPage = ref(1);
const pageCount = ref(20);
const q = ref('');
const route = useRoute();
const { id } = route.params;

const tableHeaders = [
  { key: 'notification_id', label: '#', sortable: false },
  { key: 'date_captured', label: 'Date', sortable: true },
  { key: 'motion_detected', label: 'Motion Detected', sortable: true },
  { key: 'level', label: 'Level', sortable: false },
  { key: 'status', label: 'Status', sortable: true },
  { key: 'action', label: 'Action', sortable: false },
];


const state = reactive({
  user: {
    user_id: null,
    name: null,
    gender: null,
    phone_no: null,
    username: null,
    created_at: null,
    updated_at: null,
    status: null,
    role: null,
  },
  errors: null,
});

async function fetchNotifications() {
  try {
    if (state.user.user_id) {
      const response = await fetch(`http://127.0.0.1:8000/api/notifications/showAll/${state.user.user_id}`);
      if (!response.ok) {
        throw new Error('Failed to fetch notifications');
      }
      const data = await response.json();
      notifications.value = data.notifications;
    }
  } catch (error) {
    console.error('Error fetching notifications:', error);
  }
}

async function fetchUser() {
  const params = {
    Authorization: "Bearer " + localStorage.getItem('_token'),
    Accept: "application/json"
  };
  
  try {
    const response = await $fetch(`http://127.0.0.1:8000/api/user`, {
      method: 'GET',
      headers: params
    });

    state.user.user_id = response.user_id;
    state.user.name = response.first_name + ' ' + response.last_name;
    state.user.gender = response.gender;
    state.user.phone_no = response.phone_no;
    state.user.role = response.role;
    state.user.status = response.status;
    state.user.updated_at = response.updated_at;
    state.user.username = response.username;
    state.user.created_at = response.created_at;

  } catch (error) {
    state.errors = error.response;
    console.log(error.response);
    console.log('error', error);
  }
}

onMounted(async () => {
  await fetchUser();
});

watch(
  () => state.user.user_id,
  (newVal) => {
    if (newVal) {
      fetchNotifications();
    }
  }
);

const filteredRows = computed(() => {
  if (!q.value) {
    return notifications.value;
  }
  return notifications.value.filter((notification) =>
    Object.values(notification).some((value) =>
      String(value).toLowerCase().includes(q.value.toLowerCase())
    )
  );
});

const totalNotifications = computed(() => filteredRows.value.length);

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * pageCount.value;
  const end = start + pageCount.value;
  return filteredRows.value.slice(start, end);
});

const updatePage = (page) => {
  currentPage.value = page;
};

const startItem = computed(() => {
  return (currentPage.value - 1) * pageCount.value + 1;
});

const endItem = computed(() => {
  const end = currentPage.value * pageCount.value;
  return end > totalNotifications.value ? totalNotifications.value : end;
});

const viewActionModal = (item) => {
  navigateTo(`/client/notifications/${item.notification_id}`)
};

</script>

