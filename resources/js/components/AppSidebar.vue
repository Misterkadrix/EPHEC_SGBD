<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid,  PackageSearch  } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Product',

        href: '/products',
        icon: PackageSearch,
    },
    {
        title: 'Universities',
        href: '/universities',
        icon: BookOpen,
    },
    {
        title: 'Sites',
        href: '/sites',
        icon: LayoutGrid,
    },
    {
        title: 'Salles',
        href: '/rooms',
        icon: LayoutGrid,
    },
    {
        title: 'Types d\'équipements',
        href: '/equipment-types',
        icon: LayoutGrid,
    },
    {
        title: 'Équipements',
        href: '/equipment',
        icon: LayoutGrid,
    },
    {
        title: 'Cours',
        href: '/courses',
        icon: LayoutGrid,
    },
    {
        title: 'Années académiques',
        href: '/academic-years',
        icon: LayoutGrid,
    },
    {
        title: 'Groupes',
        href: '/groups',
        icon: LayoutGrid,
    },
    {
        title: 'Sessions de cours',
        href: '/course-sessions',
        icon: LayoutGrid,
    },
    {
        title: 'Fériés',
        href: '/holidays',
        icon: LayoutGrid,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
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
