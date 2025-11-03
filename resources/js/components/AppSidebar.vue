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
import { LayoutGrid, UsersRound, Activity, Shield, Key } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();

// Check if current user is superadmin
const isSuperAdmin = computed(() => {
    const user = page.props.auth?.user as any;
    return user?.roles?.some((role: any) => role.name === 'superadmin') ?? false;
});

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard().url,
            icon: LayoutGrid,
        },
        {
            title: 'usuarios',
            href: usersIndex().url,
            icon: UsersRound,
        },
    ];

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
