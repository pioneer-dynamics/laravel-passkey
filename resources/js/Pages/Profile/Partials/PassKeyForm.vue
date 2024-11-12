<script setup>
    import {startRegistration} from "@simplewebauthn/browser";
    import ActionSection from "@/Components/ActionSection.vue";
    import { useForm } from '@inertiajs/vue3';
    import { DateTime } from 'luxon';
    import { usePage } from '@inertiajs/vue3';
    import DialogModal from "@/Components/DialogModal.vue";
    import { ref } from "vue";
    import TextInput from "@/Components/TextInput.vue";
    import SecondaryButton from "@/Components/SecondaryButton.vue";
    import PrimaryButton from "@/Components/PrimaryButton.vue";
    import InputLabel from "@/Components/InputLabel.vue";
    import ConfirmsPasswordOrPasskey from "@/Components/ConfirmsPasswordOrPasskey.vue";

    const registeringNewPasskey = ref(false)

    const registrationInProgress = ref(false)

    const nameInput = ref(null)

    const form = useForm({
        passkey: '',
        name: '',
    });

    const closeModal = () => {
        registeringNewPasskey.value = false;
        registrationInProgress.value = false
        form.reset();
        form.passkey = '';
        form.name = '';
        form.clearErrors();
    }

    const showModal = () => {
        registeringNewPasskey.value = true;
        setTimeout(() => nameInput.value?.focus(), 250);
    }

    const register = () => {
        registrationInProgress.value = true
        form.post(route('passkeys.registration-options'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                console.log('from server', usePage().props.jetstream.flash.options);
                startRegistration(JSON.parse(JSON.stringify(usePage().props.jetstream.flash.options)))
                    .then((res) =>{
                        form.passkey = res;
                        console.log(res); 
                        form.post(route('passkeys.store'), {
                            preserveScroll: true
                        })
                    })
                    .catch((err) => console.log(err))
                    .finally(() => closeModal());
            }
        })
    }

    const unregister = (passkey) => {
        form.delete(route('passkeys.destroy', {passkey: passkey.id}), {
            preserveScroll: true
        })
    }
</script>
<template>
    <ActionSection>
        <template #title>Passkeys</template>
        <template #description>Passkeys are a secure form of authentication which enables you to authenticate with your device's authentication mechanism. With passkeys you will not need to login using your password or 2FA, instead you could just use your passkey.</template>
        <template #content>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Created At
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Last Access
                            </th>
                            <th scope="col" class="px-6 py-3 text-right">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="passkey in $page.props.auth.user.passkeys" class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ passkey.name }}
                            </th>
                            <td class="px-6 py-4">
                                {{ DateTime.fromISO(passkey.created_at).toLocaleString(DateTime.DATETIME_MED) }}
                            </td>
                            <td class="px-6 py-4">
                                <span v-if="JSON.parse(passkey.public_key)?.date?.lastAccesTs">
                                    {{ DateTime.fromSeconds(JSON.parse(passkey.public_key).date.lastAccesTs).toLocaleString(DateTime.DATETIME_MED) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <ConfirmsPasswordOrPasskey :seconds="60" @confirmed="unregister(passkey)">
                                    <div class="text-right">
                                        <span class="inline-flex items-center font-medium text-red-600 dark:text-red-500 hover:underline cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-red-800">Unregister</span>
                                    </div>
                                </ConfirmsPasswordOrPasskey>
                            </td>
                        </tr>
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td colspan="4">
                                <ConfirmsPasswordOrPasskey @confirmed="showModal" :seconds="60">
                                    <div class="text-center">
                                        <span class="cursor-pointer inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-blue-800">Create a Passkey?</span>
                                    </div>
                                </ConfirmsPasswordOrPasskey>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
    </ActionSection>
    <DialogModal :show="registeringNewPasskey" @close="closeModal">
            <template #title>Create a Passkey</template>
            <template #content>
                <span class="mt-2">
                    <InputLabel for="name" value="Device Name" />
                    <TextInput
                        v-model="form.name"
                        type="text"
                        ref="nameInput"
                        class="mt-1 block w-full"
                        @keyup.enter="register"
                    />
                </span>
            </template>
            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': (form.processing || registrationInProgress || form.name?.length == 0)}"
                    :disabled="(form.processing || registrationInProgress || form.name?.length == 0)"
                    @click="register"
                >
                    Register Passkey
                </PrimaryButton>
            </template>
        </DialogModal>
</template>