<template>
	<view class="content">
		<view class="imglist">
			<image class="img" src="/static/images/xd1.jpg"></image>
		</view>
		<view class="imglist">
           <text>{{title}}</text>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				title: ''
			}
		},
		onLoad() {

		},
		methods: {

		}
	}
</script>

<style>
	.content {
		width: 100%;
		margin: 0;
		padding: 0;
	}
	.imglist {
		width: 100%;
		margin: 0;
		padding: 0;
	}
	.img {
		width: 100%;
	}



</style>
