<template>
	<view class="content">
		<view class="imglist">
			<image  :src="thumb" :mode="aspectFit"></image>
		</view>
		<view>
           <text>{{title}}</text>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				title: '保利梧桐语',
				thumb:'/static/images/xd1.jpg'
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
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		color:$uni-color-error;
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
