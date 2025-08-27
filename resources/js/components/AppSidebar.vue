<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { 
    BookOpen, LayoutGrid, Building2, MapPin, Users, Calendar, GraduationCap, Wrench, Package, Clock, Home, Rocket, Route
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: Home,
    },
    {
        title: 'Universities',
        href: '/universities',
        icon: Building2,
    },
    {
        title: 'Sites',
        href: '/sites',
        icon: MapPin,
    },
    {
        title: 'Salles',
        href: '/rooms',
        icon: LayoutGrid,
    },
    {
        title: 'Types d\'équipements',
        href: '/equipment-types',
        icon: Package,
    },
    {
        title: 'Équipements',
        href: '/equipment',
        icon: Wrench,
    },
    {
        title: 'Cours',
        href: '/courses',
        icon: GraduationCap,
    },
    {
        title: 'Années académiques',
        href: '/academic-years',
        icon: Calendar,
    },
    {
        title: 'Groupes',
        href: '/groups',
        icon: Users,
    },
    {
        title: 'Sessions de cours',
        href: '/course-sessions',
        icon: Clock,
    },
    {
        title: 'Planification automatique',
        href: '/planning',
        icon: Rocket,
    },
    {
        title: 'Déplacements',
        href: '/deplacements',
        icon: Route,
    },
    {
        title: 'Fériés',
        href: '/holidays',
        icon: Calendar,
    },
];

const footerNavItems: NavItem[] = [];
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
