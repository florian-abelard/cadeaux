import Vue from "vue";
import VueRouter from "vue-router";
import Home from "../views/Home";
import Login from "../views/Login";
import IdeaList from "../views/idea/IdeaList";
import IdeaDetail from "../views/idea/IdeaDetail";
import GiftList from "../views/gift/GiftList";
import GiftDetail from "../views/gift/GiftDetail";
import EventList from "../views/event/EventList";
import EventDetail from "../views/event/EventDetail";

Vue.use(VueRouter);

export default new VueRouter({
    mode: "history",
    routes: [
        {
            path: "/",
            name: 'home',
            component: Home,
        },
        {
            path: "/login",
            name: 'login',
            component: Login,
        },
        {
            path: "/ideas",
            name: 'ideaList',
            component: IdeaList,
        },
        {
            path: "/ideas/create",
            name: 'ideaCreate',
            component: IdeaDetail,
            meta: { showBackButton: true, formMode: 'create' },
        },
        {
            path: "/ideas/:id",
            name: 'ideaDetail',
            component: IdeaDetail,
            meta: { showBackButton: true, formMode: 'edit' },
        },
        {
            path: "/gifts",
            name: 'giftList',
            component: GiftList,
        },
        {
            path: "/gifts/create",
            name: 'giftCreate',
            component: GiftDetail,
            meta: { showBackButton: true, formMode: 'create' },
        },
        {
            path: "/gifts/:id",
            name: 'giftDetail',
            component: GiftDetail,
            meta: { showBackButton: true, formMode: 'edit' },
        },
        {
            path: "/events",
            name: 'eventList',
            component: EventList,
        },
        {
            path: "/events/create",
            name: 'eventCreate',
            component: EventDetail,
            meta: { showBackButton: true, formMode: 'create' },
        },
        {
            path: "/events/:id",
            name: 'eventDetail',
            component: EventDetail,
            meta: { showBackButton: true, formMode: 'edit' },
        },
        { path: "*", redirect: "/" }
    ]
});
