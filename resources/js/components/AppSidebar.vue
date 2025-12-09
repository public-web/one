<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as usersIndex } from '@/routes/users';
import { index as activityLogsIndex } from '@/routes/activity-logs';
import { index as rolesIndex } from '@/routes/roles';
import { index as permissionsIndex } from '@/routes/permissions';
import { type NavItem } from '@/types';
import { Link, usePage, router } from '@inertiajs/vue3';
import { LayoutGrid, UsersRound, Activity, Shield, Key, FolderKanban, ClipboardCheck } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();

// Check if current user is superadmin
const isSuperAdmin = computed(() => {
    const user = page.props.auth?.user as any;
    return user?.roles?.some((role: any) => role.name === 'superadmin') ?? false;
});

// Check if current user can manage banco de proyectos (but not superadmin)
const canManageBancoProyectos = computed(() => {
    const user = page.props.auth?.user as any;
    const isSuperAdmin = user?.roles?.some((role: any) => role.name === 'superadmin') ?? false;
    const hasBancoRole = user?.roles?.some((role: any) => role.name === 'banco-proyectos') ?? false;
    return !isSuperAdmin && hasBancoRole;
});

// Check if current user has previabilizacion-social role (but not superadmin)
const hasPreviabilizacionSocialRole = computed(() => {
    const user = page.props.auth?.user as any;
    const isSuperAdmin = user?.roles?.some((role: any) => role.name === 'superadmin') ?? false;
    const hasPreviabilizacionRole = user?.roles?.some((role: any) => role.name === 'previabilizacion-social') ?? false;
    return !isSuperAdmin && hasPreviabilizacionRole;
});

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [];

    // For previabilizacion-social role, show "Banco de Proyectos" instead of Dashboard
    if (hasPreviabilizacionSocialRole.value) {
        items.push({
            title: 'Banco de Proyectos',
            href: dashboard().url,
            icon: FolderKanban,
        });
    } else {
        items.push({
            title: 'Dashboard',
            href: dashboard().url,
            icon: LayoutGrid,
        });
    }

    // Add Users for superadmins
    if (isSuperAdmin.value) {
        items.push({
            title: 'usuarios',
            href: usersIndex().url,
            icon: UsersRound,
        });
    }

    // Add Banco de Proyectos for users with banco-proyectos role or superadmin
    if (canManageBancoProyectos.value) {
        items.push({
            title: 'Banco de Proyectos',
            href: '/banco-proyectos',
            icon: FolderKanban,
        });
    }

    // Add Previabilización Social for users with previabilizacion-social role or superadmin
    if (hasPreviabilizacionSocialRole.value) {
        items.push({
            title: 'Previabilización Social',
            href: '/previabilizacion-social/dashboard',
            icon: ClipboardCheck,
        });
    }

    // Add Roles, Permissions and Activity Logs only for superadmins
    if (isSuperAdmin.value) {
        items.push({
            title: 'Roles',
            href: rolesIndex().url,
            icon: Shield,
        });
        items.push({
            title: 'Permissions',
            href: permissionsIndex().url,
            icon: Key,
        });
        items.push({
            title: 'Activity Logs',
            href: activityLogsIndex().url,
            icon: Activity,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
