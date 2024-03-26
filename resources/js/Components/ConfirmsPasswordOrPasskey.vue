<script setup>
import { ref, reactive, nextTick } from 'vue';
import DialogModal from './DialogModal.vue';
import InputError from './InputError.vue';
import PrimaryButton from './PrimaryButton.vue';
import SecondaryButton from './SecondaryButton.vue';
import TextInput from './TextInput.vue';
import ConfirmsPasskey from './ConfirmsPasskey.vue';

const emit = defineEmits(['confirmed']);

const props = defineProps({
    title: {
        type: String,
        default: 'Confirm Authority',
    },
    content: {
        type: String,
        default: 'For your security, please confirm your authority to continue.',
    },
    button: {
        type: String,
        default: 'Confirm',
    },
    mandatory: {
        type: Boolean,
        default: false,
    },
    seconds: {
        type: Number,
        default: 0,
    }
});

const confirmingPassword = ref(false);

const passkeyConfirmation = ref(null);

const form = reactive({
    password: '',
    error: '',
    processing: false,
});

const passwordInput = ref(null);

const askForPassword = () => {
    confirmingPassword.value = true;
    setTimeout(() => passwordInput.value.focus(), 250);

}

const startConfirmingPassword = () => {
    axios.get(route('password.confirmation', props.seconds > 0 ? {seconds: props.seconds} : {})).then(response => {
        if (response.data.confirmed && !props.mandatory) {
            emit('confirmed');
        } else {
            passkeyConfirmation.value.start();
        }
    });
};

const confirmPassword = () => {
    form.processing = true;

    axios.post(route('password.confirm'), {
        password: form.password,
    }).then(() => {
        form.processing = false;

        closeModal();
        nextTick().then(() => emit('confirmed'));

    }).catch(error => {
        form.processing = false;
        form.error = error.response.data.errors.password[0];
        passwordInput.value.focus();
    });
};

const closeModal = () => {
    confirmingPassword.value = false;
    form.password = '';
    form.error = '';
};
</script>

<template>
    <span>
        <span @click="startConfirmingPassword">
            <slot />
        </span>

        <ConfirmsPasskey :__USERNAME__="$page.props.auth.user.__USERNAME__" ref="passkeyConfirmation" @confirmed="emit('confirmed')" @cancelled="askForPassword"/>

        <DialogModal :show="confirmingPassword" @close="closeModal" ref="password">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                {{ content }}

                <div class="mt-4">
                    <TextInput
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="Password"
                        autocomplete="current-password"
                        @keyup.enter="confirmPassword"
                    />

                    <InputError :message="form.error" class="mt-2" />
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    class="ms-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="confirmPassword"
                >
                    {{ button }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </span>
</template>
