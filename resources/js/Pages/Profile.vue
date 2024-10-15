<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';

// Dados do usuário logado
const user = usePage().props.auth.user;

// Formulário de atualização de perfil
const profileForm = useForm({
    name: user.name,
    email: user.email,
});

// Formulário de atualização de senha
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});
const passwordInput = ref(null);
const currentPasswordInput = ref(null);

// Lógica para deletar a conta
const confirmingUserDeletion = ref(false);
const deleteForm = useForm({ password: '' });
const deletePasswordInput = ref(null);

// Funções de submissão
const updateProfile = () => profileForm.patch(route('profile.update'));
const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
        onError: () => {
            if (passwordForm.errors.password) {
                passwordForm.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (passwordForm.errors.current_password) {
                passwordForm.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => deletePasswordInput.value.focus());
};
const deleteUser = () => {
    deleteForm.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => deletePasswordInput.value.focus(),
        onFinish: () => deleteForm.reset(),
    });
};
const closeModal = () => {
    confirmingUserDeletion.value = false;
    deleteForm.clearErrors();
    deleteForm.reset();
};
</script>

<template>
    <section>
        <!-- Atualizar Informações do Perfil -->
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Profile Information
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <form @submit.prevent="updateProfile" class="mt-6 space-y-6">
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="profileForm.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="profileForm.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="profileForm.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="profileForm.errors.email" />
            </div>

            <div v-if="user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="profileForm.processing">Save</PrimaryButton>
                <p v-if="profileForm.recentlySuccessful" class="text-sm text-gray-600">
                    Saved.
                </p>
            </div>
        </form>

        <!-- Atualizar Senha -->
        <header class="mt-12">
            <h2 class="text-lg font-medium text-gray-900">
                Update Password
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Ensure your account is using a long, random password to stay secure.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <InputLabel for="current_password" value="Current Password" />
                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="passwordForm.current_password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                />
                <InputError :message="passwordForm.errors.current_password" class="mt-2" />
            </div>

            <div>
                <InputLabel for="password" value="New Password" />
                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="passwordForm.password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <InputError :message="passwordForm.errors.password" class="mt-2" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <TextInput
                    id="password_confirmation"
                    v-model="passwordForm.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <InputError :message="passwordForm.errors.password_confirmation" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="passwordForm.processing">Save</PrimaryButton>
                <p v-if="passwordForm.recentlySuccessful" class="text-sm text-gray-600">
                    Saved.
                </p>
            </div>
        </form>

        <!-- Deletar Conta -->
        <header class="mt-12">
            <h2 class="text-lg font-medium text-gray-900">Delete Account</h2>
            <p class="mt-1 text-sm text-gray-600">
                Once your account is deleted, all of its resources and data will be permanently deleted.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion">Delete Account</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure you want to delete your account?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Please enter your password to confirm you would like to permanently delete your account.
                </p>

                <div class="mt-6">
                    <InputLabel for="password" value="Password" class="sr-only" />
                    <TextInput
                        id="password"
                        ref="deletePasswordInput"
                        v-model="deleteForm.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="Password"
                        @keyup.enter="deleteUser"
                    />
                    <InputError :message="deleteForm.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': deleteForm.processing }"
                        :disabled="deleteForm.processing"
                        @click="deleteUser"
                    >
                        Delete Account
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
