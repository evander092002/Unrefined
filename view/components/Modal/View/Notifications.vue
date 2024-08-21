<template>
  <UModal prevent-close :ui="{ background: 'bg-custom-50 dark:bg-custom-900', rounded: 'rounded' }">
    <div class="h-auto w-full p-5 flex flex-col gap-3">
      <section class="flex justify-between items-center">
        <h1 class="text-lg font-semibold">Motion Detected!</h1>
        <UIcon name="i-lucide-x" class="text-xl hover:opacity-50 cursor-pointer" @click="closeModal" />
      </section>
      <hr class="border-custom-300 dark:border-custom-700">
      <section class="flex flex-col">
        <div class="flex justify-between items-center">
          <h1 class="font-normal">Motion Detected: <span class="font-bold">{{ notification.motion_detected }}</span></h1>
          <span class="text-xs text-custom-400">Date captured: {{ notification.date_captured }}</span>
        </div>
        <div>
          <p>Description: {{ notification.motion_detected }}</p>
          <p>Level: {{ notification.level }}</p>
          <p>Status: {{ notification.status }}</p>
        </div>
        <div class="flex justify-center items-center mt-2 w-full bg-white border dark:border-custom-700">
          <img class="w-auto h-[250px]" :src="notification.snapshot" alt="Snapshot">
        </div>
      </section>
    </div>
  </UModal>
</template>

<script setup>
import { defineProps } from 'vue';
import { useModal } from '@vueuse/core';

const props = defineProps({
  notification: Object,
});

const modal = useModal();

const closeModal = () => {
  modal.close();
};

</script>
