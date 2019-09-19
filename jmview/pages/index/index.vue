<template>
	<view>
		<vrow class="content">
		    <vcol span="100"><image class="img" v-bind:src="thumb"></image></vcol>
		    <vcol span="70">
		        <vrow retract="1">{{title}}</vrow>
				<vrow class="subtitle" retract="1">{{house_type}} | {{area}} | {{price}}万</vrow>
		    </vcol>
			<vcol span="30">
			    <vrow retract="1"><button class="button">预约设计</button></vrow>
			</vcol>
		</vrow>
		<vrow class="content">
		    <vcol span="100"><image class="img" v-bind:src="thumb"></image></vcol>
		    <vcol span="70">
		        <vrow retract="1">{{title}}</vrow>
				<vrow class="subtitle" retract="1">{{house_type}} | {{area}} | {{price}}万</vrow>
		    </vcol>
			<vcol span="30">
			    <vrow retract="1"><button class="button">预约设计</button></vrow>
			</vcol>
		</vrow>
		<vrow class="content">
		    <vcol span="100"><image class="img" v-bind:src="thumb"></image></vcol>
		    <vcol span="70">
		        <vrow retract="1">{{title}}</vrow>
				<vrow class="subtitle" retract="1">{{house_type}} | {{area}} | {{price}}万</vrow>
		    </vcol>
			<vcol span="30">
			    <vrow retract="1"><button class="button">预约设计</button></vrow>
			</vcol>
		</vrow>
	</view>
</template>

<script>
	import vrow from '@/components/lml-layout/row.vue'
	import vcol from '@/components/lml-layout/col.vue'
	
	export default {
		components:{
			vrow,vcol
		},
		data() {
			return {
				title: '保利梧桐语 90平',
				thumb:'/static/images/xd1.jpg',
				introduce:'现代简约',
				house_type:'3室2厅2卫',
				area:'70-90平',
				price:15			
			}
		},
		onLoad() {

		},
		methods: {

		}
	}
</script>

<style>
	.content{
		margin:15upx 0;
	}
	.img {
		width: 100%;
	}
	.subtitle{
		font-size: 28upx;
		color:#999999;
		margin-top: 10upx;
	}
	.button{
		background:-webkit-linear-gradient(left,#695648,#82858A); /* Safari 5.1 - 6.0 */
		background:linear-gradient(left,#695648,#82858A);
		background:-ms-linear-gradient(left,#695648,#82858A);/* IE 10 */
		background:-moz-linear-gradient(left,#695648,#82858A);/*火狐*/ 
		background:-o-linear-gradient(left,#695648,#82858A); /* Opera 11.1 - 12.0 */
		color:#fff;
		margin-top: 5upx;
	}



</style>
