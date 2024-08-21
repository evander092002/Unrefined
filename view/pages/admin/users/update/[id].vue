<template>
  <div class="h-auto w-full flex flex-col p-5 gap-10">
    <section class="">
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
      <div
        class="sm:w-3/4 w-full h-auto border p-5 rounded border-custom-300 bg-custom-100 dark:bg-custom-900 dark:border-custom-700">

        <UForm class="h-auto w-full flex flex-col gap-3" :state="state" @submit="onSubmit" :validate="validate"
          @error="onError">

          <header class="flex justify-between items-center">
            <div class="font-semibold cursor-default flex items-center gap-1 w-1/2">
              <UIcon name="i-lucide-edit" class="text-xl" />
              <h1 class="font-bold text-xl">Edit User</h1>
            </div>
          </header>

          <p>-- iupdate ang info sa user -- <span class="text-xs">(ang superadmin lang maka access diri na page)</span>
          </p>
          <div class="flex justify-between">

            <h1 class="text-lg w-auto">Personal Information</h1>

            <div class="flex justify-end w-1/2 gap-x-2">

              <section class="w-auto">
                <UButton label="Cancel" icon="i-lucide-x"
                  class="flex justify-center w-full items-center rounded dark:bg-red-600 dark:hover:bg-red-500 bg-red-700 hover:bg-red-600 dark:text-custom-100"
                  :to="`/admin/users/details/${id}`" />
              </section>

              <section class="w-auto">
                <UButton :label="label" :loading-icon="loadIcon" :loading="loading" icon="i-lucide-save"
                  class="flex justify-center w-full items-center rounded dark:text-white" type="submit" />
              </section>
            </div>
          </div>

          <hr class="border-custom-300 dark:border-custom-500 w-full">

          <section class="flex w-full gap-x-2">

            <!-- first name -->
            <UFormGroup class="w-1/2" label="First name" name="first_name" :ui="{ error: 'mt-1' }">

              <template #default="{ error }">
                <UInput type="text" color="gray" v-model="state.first_name" size="md" :ui="{
                  rounded: 'rounded',
                  color: error ?
                    { red: { outline: 'bg-red-100 dark:bg-red-50 text-custom-900 dark:text-custom-900 focus:ring-1 focus:ring-red-400 border-2 border-red-400 focus:border-red-400 active:ring-red-400 active:border-red-400' } } : { gray: { outline: 'dark:bg-custom-100 dark:text-custom-900' } }
                }" />
              </template>

              <template #error="{ error }">
                <span
                  :class="[error ? 'text-red-500 dark:text-red-400 text-xs font-bold' : 'text-primary-500 dark:text-primary-400']">
                  {{ error ? error : undefined }}
                </span>
              </template>
            </UFormGroup>

            <!-- last name -->
            <UFormGroup class="w-1/2" label="Last name" name="last_name" :ui="{ error: 'mt-1' }">

              <template #default="{ error }">
                <UInput type="text" color="gray" v-model="state.last_name" size="md" :ui="{
                  rounded: 'rounded',
                  color: error ?
                    { red: { outline: 'bg-red-100 dark:bg-red-50 text-custom-900 dark:text-custom-900 focus:ring-1 focus:ring-red-400 border-2 border-red-400 focus:border-red-400 active:ring-red-400 active:border-red-400' } } : { gray: { outline: 'dark:bg-custom-100 dark:text-custom-900' } }
                }" />
              </template>

              <template #error="{ error }">
                <span
                  :class="[error ? 'text-red-500 dark:text-red-400 text-xs font-bold' : 'text-primary-500 dark:text-primary-400']">
                  {{ error ? error : undefined }}
                </span>
              </template>
            </UFormGroup>

            <!-- middle initial -->
            <UFormGroup class="w-1/4" label="M. I." name="m_i">
              <template #default="{ error }">
                <UInput type="text" color="gray" size="md" v-model="state.m_i" :ui="{
                  rounded: 'rounded',
                  color: error ?
                    { red: { outline: 'bg-red-100 dark:bg-red-50 text-custom-900 dark:text-custom-900 focus:ring-1 focus:ring-red-400 border-2 border-red-400 focus:border-red-400 active:ring-red-400 active:border-red-400' } } : { gray: { outline: 'dark:bg-custom-100 dark:text-custom-900 py-2' } }
                }" />
              </template>
              <template #error="{ error }">
                <span
                  :class="[error ? 'text-red-500 dark:text-red-400 text-xs font-bold' : 'text-primary-500 dark:text-primary-400']">
                  {{ error ? error : undefined }}
                </span>
              </template>
            </UFormGroup>
          </section>

          <section class="flex w-full gap-x-2">

            <!-- gender -->
            <UFormGroup class="w-2/3" label="Gender" name="gender">
              <URadioGroup v-model="state.gender" :options="genderOptions" class="ml-2" />
              <template #error="{ error }">
                <span
                  :class="[error ? 'text-red-500 dark:text-red-400 text-xs font-bold' : 'text-primary-500 dark:text-primary-400']">
                  {{ error ? error : undefined }}
                </span>
              </template>
            </UFormGroup>

            <!-- phone -->
            <UFormGroup class="w-full" name="phone_no">
              <template #label>
                <div class="flex items-center justify-start gap-1">
                  <p class="text-sm">Phone no.</p>
                  <UIcon name="i-emojione-v1-flag-for-philippines" />
                </div>
              </template>
              <template #default="{ error }">
                <UInput type="text" color="gray" size="md" v-model="state.phone_no" :ui="{
                  rounded: 'rounded',
                  color: error ?
                    { red: { outline: 'bg-red-100 dark:bg-red-50 text-custom-900 dark:text-custom-900 focus:ring-1 focus:ring-red-400 border-2 border-red-400 focus:border-red-400 active:ring-red-400 active:border-red-400' } } : { gray: { outline: 'dark:bg-custom-100 dark:text-custom-900' } }
                }" />
              </template>
              <template #error="{ error }">
                <span
                  :class="[error ? 'text-red-500 dark:text-red-400 text-xs font-bold' : 'text-primary-500 dark:text-primary-400']">
                  {{ error ? error : undefined }}
                </span>
              </template>
            </UFormGroup>
          </section>

          <section class="flex w-full gap-x-2">

            <!-- status -->
            <UFormGroup class="w-2/3" label="Status" name="status">
              <URadioGroup v-model="state.status" :options="statusOptions" class="ml-2"
                :uiRadio="{ color: state.status === statusOptions[0].value ? 'text-green-500' : 'text-red-500' }" />
            </UFormGroup>

            <!-- role -->
            <UFormGroup class="w-full" label="Role" name="role">
              <template #default="{ error }">
                <USelectMenu color="gray" size="md" :ui="{
                  rounded: 'rounded',
                  color: error ?
                    { red: { outline: 'bg-red-100 dark:bg-red-50 text-custom-900 dark:text-custom-900 focus:ring-1 focus:ring-red-400 border-2 border-red-400 focus:border-red-400 active:ring-red-400 active:border-red-400' } } : { gray: { outline: 'dark:bg-custom-100 dark:text-custom-900' } }
                }"
                  :uiMenu="{ background: 'dark:bg-custom-400', option: { color: 'dark:text-white', active: 'dark:bg-custom-600', empty: 'dark:text-white' }, empty: 'dark:text-white' }"
                  v-model="state.role" :options="roleOptions" placeholder="Select a role" />
              </template>
              <template #error="{ error }">
                <span
                  :class="[error ? 'text-red-500 dark:text-red-400 text-xs font-bold' : 'text-primary-500 dark:text-primary-400']">
                  {{ error ? error : undefined }}
                </span>
              </template>
            </UFormGroup>
          </section>

          <h1 class="text-lg w-auto text-start mt-3 -mb-2">Login Credentials</h1>
          <hr class="border-custom-300 dark:border-custom-500 w-full">

          <section class="flex w-full gap-x-2">

            <!-- username -->
            <UFormGroup class="w-1/2" label="Username" name="username">
              <template #default="{ error }">
                <UInput type="text" color="gray" size="md" v-model="state.username" :ui="{
                  rounded: 'rounded',
                  color: error ?
                    { red: { outline: 'bg-red-100 dark:bg-red-50 text-custom-900 dark:text-custom-900 focus:ring-1 focus:ring-red-400 border-2 border-red-400 focus:border-red-400 active:ring-red-400 active:border-red-400' } } : { gray: { outline: 'dark:bg-custom-100 dark:text-custom-900' } }
                }" />
              </template>
              <template #error="{ error }">
                <span
                  :class="[error ? 'text-red-500 dark:text-red-400 text-xs font-bold' : 'text-primary-500 dark:text-primary-400']">
                  {{ error ? error : undefined }}
                </span>
              </template>
            </UFormGroup>

            <!-- password -->
            <UFormGroup class="w-1/2" label="Password" name="password">
              <template #default="{ error }">
                <UInput type="password" color="gray" size="md" v-model="state.password" :ui="{
                  rounded: 'rounded',
                  color: error ?
                    { red: { outline: 'bg-red-100 dark:bg-red-50 text-custom-900 dark:text-custom-900 focus:ring-1 focus:ring-red-400 border-2 border-red-400 focus:border-red-400 active:ring-red-400 active:border-red-400' } } : { gray: { outline: 'dark:bg-custom-100 dark:text-custom-900' } }
                }" />
              </template>
              <template #error="{ error }">
                <span
                  :class="[error ? 'text-red-500 dark:text-red-400 text-xs font-bold' : 'text-primary-500 dark:text-primary-400']">
                  {{ error ? error : undefined }}
                </span>
              </template>
            </UFormGroup>

          </section>
        </UForm>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { useRoute } from 'vue-router';
definePageMeta({
  layout: 'sidebar'
})

import type { FormError, FormErrorEvent, FormSubmitEvent } from '#ui/types'
import { user } from '~/assets/js/userSample';

const statusOptions = [
  {
    value: 'active',
    label: 'Active'
  },
  {
    value: 'inactive',
    label: 'Inactive'
  }
];

const genderOptions = [
  {
    value: 'male',
    label: 'Male'
  },
  {
    value: 'female',
    label: 'Female'
  }
];

const roleOptions = [
  {
    value: 'client',
    label: 'client'
  },
  {
    value: 'admin',
    label: 'admin'
  },
  {
    value: 'superadmin',
    label: 'superadmin'
  }
];

const state = reactive({
  first_name: undefined,
  last_name: undefined,
  m_i: undefined,
  gender: undefined,
  phone_no: undefined,
  status: statusOptions[0].value,
  role: user.role,
  username: undefined,
  password: undefined
})

const validate = (state: any): FormError[] => {
  const errors = []
  if (!state.first_name) errors.push({ path: 'first_name', message: 'Required' })
  if (!state.last_name) errors.push({ path: 'last_name', message: 'Required' })
  if (!state.gender) errors.push({ path: 'gender', message: 'Required' })
  if (!state.phone_no) errors.push({ path: 'phone_no', message: 'Required' })
  if (!state.status) errors.push({ path: 'status', message: 'Required' })
  if (!state.username) errors.push({ path: 'username', message: 'Required' })
  if (!state.password) errors.push({ path: 'password', message: 'Required' })
  return errors
}

const loading = ref(false);
const loadIcon = ref('');
const label = ref('Update');
const route = useRoute();
const { id } = route.params;

async function onSubmit(event: FormSubmitEvent<any>) {
  try {
    console.log('Submitting with data:', event.data);
    loading.value = true;
    loadIcon.value = 'i-lucide-loader-circle';
    label.value = '';

    // Extract the role value from event.data
    const roleValue = event.data.role.value;

    // Validate role against allowed values
    if (!['admin', 'client', 'superadmin'].includes(roleValue)) {
      console.error('Invalid role value:', roleValue);
      // Handle invalid role value error (e.g., show a message to the user)
      return;
    }

    const response = await fetch(`http://127.0.0.1:8000/api/users/${id}/edit`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ ...event.data, role: roleValue }), // Ensure role is sent as a string
    });

    const responseData = await response.json();
    console.log('Response from server:', responseData);

    if (response.ok) {
      // Update local state with the updated user data
      state.first_name = responseData.data.first_name;
      state.last_name = responseData.data.last_name;
      state.m_i = responseData.data.middle_initial;
      state.gender = responseData.data.gender;
      state.phone_no = responseData.data.phone_no;
      state.status = responseData.data.status;
      state.role = roleValue; // Update with roleValue instead of responseData.data.role.value
      state.username = responseData.data.username;

      // Navigate to details page or any other appropriate action
      navigateTo(`/admin/users/details/${id}`);
    } else {
      // Handle errors from API
      console.error('Failed to update user:', responseData.message);
      // Display an error message or handle it accordingly
    }
  } catch (error) {
    console.error('Error updating user:', error);
  } finally {
    loading.value = false;
    loadIcon.value = '';
    label.value = 'Update';
  }
}


async function onError(event: FormErrorEvent) {
  const element = document.getElementById(event.errors[0].id)
  element?.focus()
  element?.scrollIntoView({ behavior: 'smooth', block: 'center' })
}

const links = [{
  label: 'Users',
  to: '/admin/users'
}, {
  label: 'Details',
  to: `/admin/users/details/${id}`
}, {
  label: 'Update',
}
]
</script>
