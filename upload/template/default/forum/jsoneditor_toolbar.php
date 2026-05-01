<?php exit('Access Denied');?>
<!--{eval $jsonEditorToolbar = array();}-->
<!--{eval foreach ($editorblocks as $item) {;}-->
	<!--{eval if (isset($item['identifier']) && isset($item['available'])) {;}-->
		<!--{eval $jsonEditorToolbar[$item['identifier']] = $item['available'];}-->
	<!--{eval };}-->
<!--{eval };}-->
<div id="json-editor-toolbar" class="toolbar">
	<div class="mainArea toolbar-area">
		<div class="toolbar-section">
			<div id="toolbar-undo">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onclick="return blockEvent('undo', undefined, event)">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1746778418078" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="8535" width="30" height="30"><path d="M511.4 124C290.5 124.3 112 303 112 523.9c0 128 60.2 242 153.8 315.2l-37.5 48c-4.1 5.3-0.3 13 6.3 12.9l167-0.8c5.2 0 9-4.9 7.7-9.9L369.8 727c-1.6-6.5-10-8.3-14.1-3L315 776.1c-10.2-8-20-16.7-29.3-26-29.4-29.4-52.5-63.6-68.6-101.7C200.4 609 192 567.1 192 523.9s8.4-85.1 25.1-124.5c16.1-38.1 39.2-72.3 68.6-101.7 29.4-29.4 63.6-52.5 101.7-68.6C426.9 212.4 468.8 204 512 204s85.1 8.4 124.5 25.1c38.1 16.1 72.3 39.2 101.7 68.6 29.4 29.4 52.5 63.6 68.6 101.7 16.7 39.4 25.1 81.3 25.1 124.5s-8.4 85.1-25.1 124.5c-16.1 38.1-39.2 72.3-68.6 101.7-7.5 7.5-15.3 14.5-23.4 21.2-3.4 2.8-3.9 7.7-1.2 11.1l39.4 50.5c2.8 3.5 7.9 4.1 11.4 1.3C854.5 760.8 912 649.1 912 523.9c0-221.1-179.4-400.2-400.6-399.9z" p-id="8536"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_undo}</span>
					</div>
				</button>
			</div>
			<div id="toolbar-redo">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onclick="return blockEvent('redo', undefined, event)">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1746778436902" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="8745" width="30" height="30"><path d="M758.2 839.1C851.8 765.9 912 651.9 912 523.9 912 303 733.5 124.3 512.6 124 291.4 123.7 112 302.8 112 523.9c0 125.2 57.5 236.9 147.6 310.2 3.5 2.8 8.6 2.2 11.4-1.3l39.4-50.5c2.7-3.4 2.1-8.3-1.2-11.1-8.1-6.6-15.9-13.7-23.4-21.2-29.4-29.4-52.5-63.6-68.6-101.7C200.4 609 192 567.1 192 523.9s8.4-85.1 25.1-124.5c16.1-38.1 39.2-72.3 68.6-101.7 29.4-29.4 63.6-52.5 101.7-68.6C426.9 212.4 468.8 204 512 204s85.1 8.4 124.5 25.1c38.1 16.1 72.3 39.2 101.7 68.6 29.4 29.4 52.5 63.6 68.6 101.7 16.7 39.4 25.1 81.3 25.1 124.5s-8.4 85.1-25.1 124.5c-16.1 38.1-39.2 72.3-68.6 101.7-9.3 9.3-19.1 18-29.3 26L668.2 724c-4.1-5.3-12.5-3.5-14.1 3l-39.6 162.2c-1.2 5 2.6 9.9 7.7 9.9l167 0.8c6.7 0 10.5-7.7 6.3-12.9l-37.3-47.9z" p-id="8746"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_redo}</span>
					</div>
				</button>
			</div>
			<!--{if $jsonEditorToolbar['clearFormatting']}-->
			<div id="toolbar-clearFormatting">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onclick="return blockEvent('clearFormatting', undefined, event)">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1747118184839" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="11763" width="30" height="30"><path d="M382.805333 910.222222H285.696L429.226667 234.154667H184.888889l17.92-85.504h585.841778l-17.92 85.504H526.336L382.862222 910.222222z m315.164445-159.971555l-110.819556 110.136889-46.990222-49.834667 110.535111-110.478222-110.535111-110.535111 46.990222-49.834667 110.819556 110.136889 110.535111-110.136889 46.933333 49.834667-110.535111 110.535111 110.535111 110.478222-46.933333 49.834667-110.535111-110.136889z" fill="#2c2c2c" p-id="11764"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_clearFormatting}</span>
					</div>
				</button>
			</div>
			<!--{/if}-->
			<span class="toolbar-divider"></span>
			<!--{if $jsonEditorToolbar['paragraph']}-->
			<div id="toolbar-paragraph">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onclick="return addBlock('paragraph', undefined, event)">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1746790981697" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="21330" width="30" height="30"><path d="M792.149333 289.557333V157.696a8.256 8.256 0 0 0-8.234666-8.256H223.424a8.256 8.256 0 0 0-8.234667 8.256v131.861333c0 4.544 3.690667 8.256 8.234667 8.256h57.706667a8.256 8.256 0 0 0 8.234666-8.256v-65.92h173.098667v576.96h-94.805333a8.256 8.256 0 0 0-8.234667 8.234667v57.706667c0 4.522667 3.712 8.234667 8.234667 8.234666h272a8.256 8.256 0 0 0 8.256-8.234666v-57.706667a8.256 8.256 0 0 0-8.256-8.234667h-94.784V223.637333h173.098666v65.92c0 4.544 3.712 8.256 8.234667 8.256h57.706667a8.256 8.256 0 0 0 8.234666-8.256z" fill="#000000" p-id="21331"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_paragraph}</span>
					</div>
				</button>
			</div>
			<!--{/if}-->
			<!--{if $jsonEditorToolbar['header']}-->
			<div id="toolbar-header">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onmouseover="bindPopover('toolbar-header', 'popover-header')">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1746780896958" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="15758" width="30" height="30"><path d="M725.333333 469.333333V170.666667h85.333334v725.333333h-85.333334v-341.333333H298.666667v341.333333H213.333333V170.666667h85.333334v298.666666h426.666666z" p-id="15759"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_header}</span>
					</div>
				</button>
			</div>
			<!--{/if}-->
			<div id="toolbar-bold">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onclick="return blockEvent('bold', undefined, event)">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1746842416325" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="31979" width="30" height="30"><path d="M437.333333 793.6h4.266667c19.2 0 34.133333 2.133333 44.8 2.133333 57.6 0 100.266667-10.666667 125.866667-32 25.6-21.333333 40.533333-55.466667 40.533333-104.533333 0-42.666667-12.8-74.666667-36.266667-96-25.6-21.333333-61.866667-32-110.933333-32H469.333333c-10.666667 0-19.2 0-29.866666 2.133333v260.266667z m0-320h27.733334c51.2 0 89.6-10.666667 113.066666-32 23.466667-21.333333 36.266667-55.466667 36.266667-100.266667 0-40.533333-10.666667-68.266667-34.133333-87.466666-21.333333-19.2-57.6-27.733333-104.533334-27.733334-8.533333 0-19.2 0-36.266666 2.133334h-2.133334v245.333333zM234.666667 170.666667h296.533333c72.533333 0 128 12.8 164.266667 40.533333 36.266667 25.6 55.466667 66.133333 55.466666 119.466667 0 40.533333-12.8 72.533333-36.266666 100.266666-23.466667 27.733333-59.733333 46.933333-104.533334 59.733334 57.6 0 100.266667 14.933333 132.266667 44.8 32 27.733333 46.933333 68.266667 46.933333 119.466666 0 64-21.333333 113.066667-64 147.2-42.666667 34.133333-104.533333 51.2-185.6 51.2H234.666667v-49.066666l57.6-4.266667 17.066666-14.933333V238.933333l-17.066666-14.933333-57.6-4.266667V170.666667z m0 0" fill="#2c2c2c" p-id="31980"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_bold}</span>
					</div>
				</button>
			</div>
			<div id="toolbar-italic">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onclick="return blockEvent('italic', undefined, event)">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1746842221517" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="18592" width="30" height="30"><path d="M716.8 164.736v80l-89.28-0.064-148.288 534.4h101.056v80H307.2v-80h89.024l148.288-534.4H443.712v-80z" fill="#212121" p-id="18593"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_italic}</span>
					</div>
				</button>
			</div>
			<div id="toolbar-underline">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onclick="return blockEvent('underline', undefined, event)">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1746842380530" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="30119" width="30" height="30"><path d="M519.68 781.76c68.864 0 123.52-20.16 164.16-60.48 40.64-40.32 60.928-102.464 60.928-186.432V236.352l54.848-10.496v-50.048H597.952v50.048l69.376 11.264v304.192c0 59.712-10.752 106.112-32.256 139.2-21.568 33.088-54.656 49.6-99.264 49.6-90.88 0-136.32-64-136.32-192V237.12l68.48-11.264v-50.048H224.448v50.048l53.248 10.496v291.2c0 87.68 19.84 152 59.712 192.896 39.808 40.896 100.544 61.312 182.336 61.312zM192 832h640v64H192z" fill="#000000" p-id="30120"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_underline}</span>
					</div>
				</button>
			</div>
			<!--{if $jsonEditorToolbar['emoji']}-->
			<div id="toolbar-emoji">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onclick="return blockEvent('emoji', undefined, event)">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1761144282987" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="1805" width="30" height="30"><path d="M512 85.290667C747.648 85.290667 938.666667 276.352 938.666667 512.042667c0 235.648-191.061333 426.709333-426.709334 426.709333-235.690667 0-426.752-191.061333-426.752-426.709333C85.248 276.352 276.309333 85.248 512 85.248z m0 64a362.752 362.752 0 1 0 0 725.461333A362.752 362.752 0 0 0 512 149.333333zM360.96 630.784A191.616 191.616 0 0 0 512 704.085333a191.616 191.616 0 0 0 150.784-73.130666 32 32 0 0 1 50.261333 39.68A255.616 255.616 0 0 1 512 768.085333a255.616 255.616 0 0 1-201.258667-97.706666 32 32 0 0 1 50.261334-39.594667zM384 373.333333a53.333333 53.333333 0 1 1 0 106.624A53.333333 53.333333 0 0 1 384 373.333333z m256 0a53.333333 53.333333 0 1 1 0 106.624 53.333333 53.333333 0 0 1 0-106.624z" fill="#212121" p-id="1806"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_emoji}</span>
					</div>
				</button>
			</div>
			<!--{/if}-->
			<!--{if $jsonEditorToolbar['list']}-->
			<div id="toolbar-list">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onmouseover="bindPopover('toolbar-list', 'popover-list')">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1746791207102" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="23398" width="30" height="30"><path d="M903.734 456.037h-559.62c-30.905 0-55.963 25.057-55.963 55.963 0 30.877 25.057 55.963 55.963 55.963h559.62c30.933 0 55.963-25.086 55.963-55.963-0.001-30.905-25.031-55.963-55.963-55.963z m-783.468 0c-30.905 0-55.963 25.057-55.963 55.963 0 30.877 25.057 55.963 55.963 55.963s55.962-25.086 55.962-55.963c0-30.905-25.058-55.963-55.962-55.963z m0-279.81c-30.905 0-55.963 25.057-55.963 55.963s25.057 55.963 55.963 55.963 55.962-25.057 55.962-55.963-25.058-55.962-55.962-55.962z m223.848 111.925h559.62c30.933 0 55.963-25.057 55.963-55.963s-25.03-55.962-55.963-55.962h-559.62c-30.905 0-55.963 25.056-55.963 55.962s25.058 55.963 55.963 55.963z m559.62 447.696h-559.62c-30.905 0-55.963 25.086-55.963 55.963s25.057 55.962 55.963 55.962h559.62c30.933 0 55.963-25.085 55.963-55.962s-25.031-55.963-55.963-55.963z m-783.468 0c-30.905 0-55.963 25.086-55.963 55.963s25.057 55.962 55.963 55.962 55.962-25.085 55.962-55.962-25.058-55.963-55.962-55.963z" p-id="23399"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_list}</span>
					</div>
				</button>
			</div>
			<!--{/if}-->
			<!--{if $jsonEditorToolbar['quote']}-->
			<div id="toolbar-quote">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onclick="return addBlock('quote', undefined, event)">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1746790876928" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="12956" width="30" height="30"><path d="M635.072 767.872a11.52 11.52 0 0 1-7.68-3.52 17.984 17.984 0 0 1-4.608-8.64l-12.288-48a21.12 21.12 0 0 1 1.344-13.952 15.04 15.04 0 0 1 3.968-5.12 10.816 10.816 0 0 1 5.248-2.368 126.848 126.848 0 0 0 68.288-33.92 184.96 184.96 0 0 0 46.72-73.216h-6.72a312.32 312.32 0 0 1-84.48-11.776 74.048 74.048 0 0 1-39.68-30.08 113.408 113.408 0 0 1-18.88-54.592l-0.448-11.264c0-43.648 2.56-87.04 7.68-130.048 2.688-22.4 11.264-42.88 24.192-58.048s29.44-24.128 46.848-25.408c14.848-1.92 29.568-1.92 44.416-1.92h40.32c35.392 2.688 64.768 36.544 71.104 81.92 7.68 52.736 11.52 106.24 11.52 160 0.512 69.376-19.84 136.256-56.512 186.624-36.736 50.304-87.04 80.192-140.352 83.328z m-393.856 0a11.52 11.52 0 0 1-7.68-3.52 17.984 17.984 0 0 1-4.608-8.64l-12.288-48a21.12 21.12 0 0 1 1.408-13.952 15.04 15.04 0 0 1 3.904-5.12 10.816 10.816 0 0 1 5.248-2.368 126.784 126.784 0 0 0 68.352-33.92c20.096-18.816 36.224-44.032 46.656-73.216h-6.72a312.32 312.32 0 0 1-84.416-11.776 74.048 74.048 0 0 1-39.744-30.08 113.408 113.408 0 0 1-18.88-54.592L192 471.424c0-43.648 2.56-87.04 7.68-130.048 2.688-22.4 11.264-42.816 24.192-57.984 12.992-15.168 29.504-24.064 46.912-25.344 14.784-1.92 29.504-1.92 44.352-1.92h40.32c35.456 2.624 64.832 36.48 71.104 81.92 7.68 52.672 11.584 106.24 11.584 160 0.448 69.376-19.84 136.256-56.576 186.56-36.736 50.368-87.04 80.256-140.352 83.392v-0.128z" fill="#000000" fill-opacity=".9" p-id="12957"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_quote}</span>
					</div>
				</button>
			</div>
			<!--{/if}-->
			<!--{if $jsonEditorToolbar['delimiter']}-->
			<div id="toolbar-delimiter">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onclick="return addBlock('delimiter', undefined, event)">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1746790541877" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3616" width="30" height="30"><path d="M944.3555552 557.51111147h-864.7111104c-27.30666667 0-45.51111147-18.2044448-45.51111147-45.51111147s18.2044448-45.51111147 45.51111147-45.51111147h864.7111104c27.30666667 0 45.51111147 18.2044448 45.51111147 45.51111147s-18.2044448 45.51111147-45.51111147 45.51111147zM580.26666667 876.08888853h-136.53333334c-27.30666667 0-45.51111147-18.2044448-45.51111146-45.5111104s18.2044448-45.51111147 45.51111146-45.51111146h136.53333334c27.30666667 0 45.51111147 18.2044448 45.51111146 45.51111146s-18.2044448 45.51111147-45.51111146 45.5111104zM944.3555552 876.08888853h-136.53333333c-27.30666667 0-45.51111147-18.2044448-45.5111104-45.5111104s18.2044448-45.51111147 45.5111104-45.51111146h136.53333333c27.30666667 0 45.51111147 18.2044448 45.51111147 45.51111146s-18.2044448 45.51111147-45.51111147 45.5111104zM580.26666667 238.93333333h-136.53333334c-27.30666667 0-45.51111147-18.2044448-45.51111146-45.51111146s18.2044448-45.51111147 45.51111146-45.5111104h136.53333334c27.30666667 0 45.51111147 18.2044448 45.51111146 45.5111104s-18.2044448 45.51111147-45.51111146 45.51111146zM216.17777813 876.08888853h-136.53333333c-27.30666667 0-45.51111147-18.2044448-45.51111147-45.5111104s18.2044448-45.51111147 45.51111147-45.51111146h136.53333333c27.30666667 0 45.51111147 18.2044448 45.5111104 45.51111146s-18.2044448 45.51111147-45.5111104 45.5111104zM216.17777813 238.93333333h-136.53333333c-27.30666667 0-45.51111147-18.2044448-45.51111147-45.51111146s18.2044448-45.51111147 45.51111147-45.5111104h136.53333333c27.30666667 0 45.51111147 18.2044448 45.5111104 45.5111104s-18.2044448 45.51111147-45.5111104 45.51111146zM944.3555552 238.93333333h-136.53333333c-27.30666667 0-45.51111147-18.2044448-45.5111104-45.51111146s18.2044448-45.51111147 45.5111104-45.5111104h136.53333333c27.30666667 0 45.51111147 18.2044448 45.51111147 45.5111104s-18.2044448 45.51111147-45.51111147 45.51111146z" fill="#333333" p-id="3617"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_delimiter}</span>
					</div>
				</button>
			</div>
			<!--{/if}-->
			<span class="toolbar-divider"></span>
			<!--{if $jsonEditorToolbar['image']}-->
			<div id="toolbar-image">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onclick="return addBlock('image', undefined, event)">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1746780820116" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="8535" width="30" height="30"><path d="M928 160H96c-17.7 0-32 14.3-32 32v640c0 17.7 14.3 32 32 32h832c17.7 0 32-14.3 32-32V192c0-17.7-14.3-32-32-32z m-40 632H136v-39.9l138.5-164.3 150.1 178L658.1 489 888 761.6V792z m0-129.8L664.2 396.8c-3.2-3.8-9-3.8-12.2 0L424.6 666.4l-144-170.7c-3.2-3.8-9-3.8-12.2 0L136 652.7V232h752v430.2z" p-id="8536"></path><path d="M304 456c48.6 0 88-39.4 88-88s-39.4-88-88-88-88 39.4-88 88 39.4 88 88 88z m0-116c15.5 0 28 12.5 28 28s-12.5 28-28 28-28-12.5-28-28 12.5-28 28-28z" p-id="8537"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_image}</span>
					</div>
				</button>
			</div>
			<!--{/if}-->
			<!--{if $jsonEditorToolbar['video']}-->
			<div id="toolbar-video">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onclick="return addBlock('video', undefined, event)">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1746790341802" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="7979" width="30" height="30"><path d="M853.333333 195.047619a73.142857 73.142857 0 0 1 73.142857 73.142857v487.619048a73.142857 73.142857 0 0 1-73.142857 73.142857H170.666667a73.142857 73.142857 0 0 1-73.142857-73.142857V268.190476a73.142857 73.142857 0 0 1 73.142857-73.142857h682.666666z m0 73.142857H170.666667v487.619048h682.666666V268.190476z m-414.086095 84.406857a48.761905 48.761905 0 0 1 24.185905 6.412191l194.291809 111.030857a48.761905 48.761905 0 0 1 0 84.675048l-194.31619 111.006476a48.761905 48.761905 0 0 1-72.923429-42.325334v-222.037333a48.761905 48.761905 0 0 1 48.761905-48.761905z m24.380952 90.745905v138.020572l120.758858-68.998096-120.734477-68.998095z" p-id="7980"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_video}</span>
					</div>
				</button>
			</div>
			<!--{/if}-->
			<!--{if $jsonEditorToolbar['audio']}-->
			<div id="toolbar-audio">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onclick="return addBlock('audio', undefined, event)">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1746790698317" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="7549" width="30" height="30"><path d="M468.992 169.6c29.312-22.528 64.128-40.832 101.312-25.088 36.864 15.552 48.64 53.12 53.76 89.984 5.248 37.824 5.248 89.92 5.248 154.688V634.88c0 64.768 0 116.864-5.184 154.688-5.12 36.928-16.96 74.432-53.76 89.984-37.248 15.744-72.064-2.56-101.376-25.024-30.016-23.04-66.112-59.904-110.912-105.6l-1.92-2.048c-23.04-23.488-38.336-34.88-53.76-41.28-15.616-6.4-34.496-9.152-67.456-9.152h-1.664c-28.544 0-52.416 0-71.68-1.984-20.288-2.112-39.104-6.72-56.064-18.24-32.192-22.016-44.544-54.208-49.28-83.84C52.864 570.24 53.248 545.92 53.568 526.464a907.84 907.84 0 0 0 0-28.928C53.184 478.08 52.864 453.76 56.32 431.68c4.672-29.568 17.024-61.824 49.28-83.84 16.896-11.52 35.712-16.128 55.936-18.176a750.72 750.72 0 0 1 71.68-2.048h1.728c32.96 0 51.84-2.688 67.392-9.152 15.488-6.4 30.72-17.728 53.76-41.216l1.984-2.048c44.8-45.76 80.896-82.56 110.912-105.6z m38.976 50.752c-25.92 19.84-58.88 53.44-106.112 101.632-25.152 25.6-47.616 44.288-75.072 55.68-27.328 11.264-56.32 13.952-91.84 13.952-30.656 0-51.2 0-66.752 1.664-15.04 1.6-21.952 4.352-26.56 7.488-12.416 8.448-19.008 21.184-22.144 40.96-2.56 16-2.24 32.512-1.92 51.136l0.128 19.2c0 6.592-0.064 12.992-0.192 19.136-0.256 18.56-0.512 35.072 1.984 51.136 3.136 19.712 9.728 32.512 22.144 40.96 4.608 3.136 11.52 5.888 26.56 7.424 15.616 1.6 36.096 1.664 66.752 1.664 35.456 0 64.512 2.688 91.84 14.016 27.456 11.328 49.92 29.952 75.072 55.616 47.232 48.192 80.192 81.728 106.112 101.696 27.008 20.736 35.136 17.856 37.44 16.832 2.624-1.088 10.56-5.44 15.296-39.808 4.544-32.896 4.608-80.512 4.608-148.672V391.936c0-68.096 0-115.712-4.608-148.608-4.736-34.368-12.672-38.784-15.36-39.872-2.24-0.96-10.368-3.84-37.376 16.896zM705.92 358.592a32 32 0 0 1 44.864 6.016c30.912 40.448 49.28 91.776 49.28 147.392s-18.368 106.88-49.28 147.392a32 32 0 1 1-50.88-38.784A178.56 178.56 0 0 0 736 512a178.56 178.56 0 0 0-36.096-108.608 32 32 0 0 1 6.016-44.8zM876.928 277.056a32 32 0 0 0-47.168 43.2c48.448 52.992 76.928 119.68 76.928 191.744s-28.48 138.752-76.928 191.68a32 32 0 0 0 47.168 43.264c58.24-63.616 93.76-145.408 93.76-234.944 0-89.6-35.52-171.328-93.76-234.944z" fill="#333333" p-id="7550"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_audio}</span>
					</div>
				</button>
			</div>
			<!--{/if}-->
			<!--{if $jsonEditorToolbar['attaches']}-->
			<div id="toolbar-attaches">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onclick="return addBlock('attaches', undefined, event)">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1761618694747" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2661" width="30" height="30"><path d="M800 556.8L524.8 832c-38.4 38.4-83.2 64-140.8 64s-102.4-25.6-140.8-64c-76.8-76.8-76.8-204.8 0-281.6l384-384c25.6-25.6 57.6-38.4 89.6-38.4 32 0 64 12.8 89.6 38.4 44.8 44.8 44.8 128 0 172.8l-384 384c-6.4 6.4-19.2 12.8-32 12.8s-25.6-6.4-32-12.8c-19.2-19.2-19.2-44.8 0-64l384-384c12.8-12.8 12.8-32 0-44.8-6.4-6.4-12.8-12.8-25.6-12.8-6.4 0-19.2 6.4-25.6 12.8l-384 384c-19.2 19.2-32 51.2-32 76.8 0 32 12.8 57.6 32 76.8 19.2 19.2 51.2 32 76.8 32 32 0 57.6-12.8 76.8-32l384-384c38.4-38.4 57.6-83.2 57.6-134.4s-19.2-96-57.6-134.4c-32-32-76.8-51.2-128-51.2s-96 19.2-134.4 57.6l-384 384c-51.2 51.2-76.8 115.2-76.8 185.6 0 70.4 25.6 140.8 76.8 185.6C249.6 934.4 313.6 960 384 960s140.8-25.6 185.6-76.8L844.8 608c12.8-12.8 12.8-32 0-44.8-12.8-19.2-32-12.8-44.8-6.4z" fill="#333333" p-id="2662"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_attaches}</span>
					</div>
				</button>
			</div>
			<!--{/if}-->
			<!--{if $jsonEditorToolbar['table']}-->
			<div id="toolbar-table">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onclick="return addBlock('table', undefined, event)">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1746790415200" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="8173" width="30" height="30"><path d="M828.952381 121.904762a73.142857 73.142857 0 0 1 73.142857 73.142857v633.904762a73.142857 73.142857 0 0 1-73.142857 73.142857H195.047619a73.142857 73.142857 0 0 1-73.142857-73.142857V195.047619a73.142857 73.142857 0 0 1 73.142857-73.142857h633.904762zM316.952381 719.238095H195.047619V828.952381h121.904762v-109.714286z m512 0H390.095238V828.952381h438.857143v-109.714286zM316.952381 536.380952H195.047619v109.714286h121.904762V536.380952z m512 0H390.095238v109.714286h438.857143V536.380952zM195.047619 353.52381V463.238095h121.904762v-109.714285H195.047619zM828.952381 195.047619H195.047619v85.333333h633.904762V195.047619zM390.095238 463.238095h438.857143v-109.714285H390.095238V463.238095z" p-id="8174"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_table}</span>
					</div>
				</button>
			</div>
			<!--{/if}-->
			<!--{if $jsonEditorToolbar['codeflask']}-->
			<div id="toolbar-codeflask">
				<button type="button"
				        class="Button Button--plain Button--style"
				        onclick="return addBlock('codeflask', undefined, event)">
					<div class="button-area">
						<span style="display:inline-flex;align-items:center">
							<svg t="1746790617990" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="5521" width="30" height="30"><path d="M958.17 447.4L760.69 249.92l-65.82 65.83 197.47 197.47L694.87 710.7l65.82 65.82 197.48-197.47 65.83-65.83zM263.3 249.92L65.82 447.4 0 513.22l65.82 65.83L263.3 776.52l65.82-65.82-197.47-197.48 197.47-197.47zM343.247 949.483L590.96 52.19l89.72 24.768-247.713 897.295z" fill="#231815" p-id="5522"></path></svg>
						</span>
						<span class="button-text">{lang json_editor_toolbar_code}</span>
					</div>
				</button>
			</div>
			<!--{/if}-->
			<!--{hook/post_jsoneditor_toolbar}-->
		</div>
	</div>
</div>
<div id="json-editor-toolbar-popover">
	<div class="Popover-content Popover-content--bottom Popover-content-enter-done Popover-hidden"
	     id="popover-header">
		<div class="Menu">
			<button type="button"
			        class="Button Popover-Button--plain Popover-Button--style"
			        onclick="return convertBlock('header', { level: 1 }, event)">
				<span style="display: inline-flex; align-items: center;">
					<svg t="1746782721639" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3942" width="30" height="30"><path d="M170.666667 128H85.333333v768h85.333334V554.666667h341.333333v341.333333h85.333333V128h-85.333333v341.333333H170.666667V128z m725.333333 277.333333h-74.069333l-137.002667 68.501334 38.144 76.330666L810.666667 506.368V896h85.333333V405.333333z" p-id="3943"></path></svg>
				</span>
				<span class="popover-button-text">&nbsp;{lang json_editor_toolbar_header1}</span>
			</button>
			<button type="button"
			        class="Button Popover-Button--plain Popover-Button--style"
			        onclick="return convertBlock('header', { level: 2 }, event)">
				<span style="display: inline-flex; align-items: center;">
					<svg t="1746782739401" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="4106" width="30" height="30"><path d="M170.666667 128H85.333333v768h85.333334V554.666667h341.333333v341.333333h85.333333V128h-85.333333v341.333333H170.666667V128z m554.666666 426.666667c0-30.976 30.144-64 64-64 39.68 0 64 27.989333 64 53.333333 0 10.410667-4.309333 23.317333-13.376 38.613333-8.64 14.570667-19.349333 27.626667-29.226666 39.68l-0.96 1.194667L640 827.221333V896h298.666667v-85.333333h-173.802667l110.72-132.842667 0.106667-0.128 1.792-2.197333c9.429333-11.498667 23.808-29.056 35.84-49.322667C926.08 604.757333 938.666667 576.256 938.666667 544c0-80.704-71.189333-138.666667-149.333334-138.666667-83.968 0-149.333333 74.261333-149.333333 149.333334h85.333333z" p-id="4107"></path></svg>
				</span>
				<span class="popover-button-text">&nbsp;{lang json_editor_toolbar_header2}</span>
			</button>
			<button type="button"
			        class="Button Popover-Button--plain Popover-Button--style"
			        onclick="return convertBlock('header', { level: 3 }, event)">
				<span style="display: inline-flex; align-items: center;">
					<svg t="1746782798997" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="4272" width="30" height="30"><path d="M170.666667 128H85.333333v768h85.333334V554.666667h341.333333v341.333333h85.333333V128h-85.333333v341.333333H170.666667V128z m606.826666 256a137.472 137.472 0 0 0-137.472 137.472h85.333334A52.138667 52.138667 0 0 1 777.493333 469.333333h11.861334a64 64 0 0 1 0 128h-42.666667v85.333334h42.666667a64 64 0 0 1 0 128h-11.861334a52.138667 52.138667 0 0 1-52.138666-52.138667h-85.333334A137.472 137.472 0 0 0 777.493333 896h11.861334A149.333333 149.333333 0 0 0 893.866667 640 149.333333 149.333333 0 0 0 789.333333 384h-11.861333z" p-id="4273"></path></svg>
				</span>
				<span class="popover-button-text">&nbsp;{lang json_editor_toolbar_header3}</span>
			</button>
			<button type="button"
			        class="Button Popover-Button--plain Popover-Button--style"
			        onclick="return convertBlock('header', { level: 4 }, event)">
				<span style="display: inline-flex; align-items: center;">
					<svg t="1746782812896" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="4436" width="30" height="30"><path d="M85.333333 128h85.333334v341.333333h341.333333V128h85.333333v768h-85.333333V554.666667H170.666667v341.333333H85.333333V128z m721.066667 256h100.266667v341.333333H938.666667v85.333334h-32v85.333333h-85.333334v-85.333333H618.666667v-74.666667L806.4 384z m14.933333 341.333333v-188.010666L721.066667 725.333333h100.266666z" p-id="4437"></path></svg>
				</span>
				<span class="popover-button-text">&nbsp;{lang json_editor_toolbar_header4}</span>
			</button>
			<button type="button"
			        class="Button Popover-Button--plain Popover-Button--style"
			        onclick="return convertBlock('header', { level: 5 }, event)">
				<span style="display: inline-flex; align-items: center;">
					<svg t="1746782822233" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="4598" width="30" height="30"><path d="M170.666667 128H85.333333v768h85.333334V554.666667h341.333333v341.333333h85.333333V128h-85.333333v341.333333H170.666667V128z m746.666666 277.333333H650.666667v277.333334h66.261333l10.346667-6.890667 0.042666-0.042667a97.792 97.792 0 0 1 13.930667-6.314666A140.928 140.928 0 0 1 789.333333 661.333333a64 64 0 0 1 64 64v21.333334a64 64 0 0 1-64 64c-14.165333 0-32.704-4.522667-49.28-13.098667-17.194667-8.896-26.154667-18.901333-28.949333-25.322667l-78.208 34.176c13.653333 31.253333 41.514667 53.248 67.925333 66.922667C727.872 887.36 759.765333 896 789.333333 896a149.333333 149.333333 0 0 0 149.333334-149.333333v-21.333334a149.333333 149.333333 0 0 0-149.333334-149.333333c-20.309333 0-38.229333 2.56-53.333333 6.122667V490.666667H917.333333v-85.333334z" p-id="4599"></path></svg>
				</span>
				<span class="popover-button-text">&nbsp;{lang json_editor_toolbar_header5}</span>
			</button>
			<button type="button"
			        class="Button Popover-Button--plain Popover-Button--style"
			        onclick="return convertBlock('header', { level: 6 }, event)">
				<span style="display: inline-flex; align-items: center;">
					<svg t="1746782832707" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="4762" width="30" height="30"><path d="M170.666667 128H85.333333v768h85.333334V554.666667h341.333333v341.333333h85.333333V128h-85.333333v341.333333H170.666667V128z m678.4 373.269333c-56.341333-5.653333-109.376 32.661333-121.258667 88.725334A140.586667 140.586667 0 0 1 789.333333 576c86.208 0 149.333333 75.562667 149.333334 160 0 84.458667-63.125333 160-149.333334 160s-149.333333-75.541333-149.333333-160v-122.24c0-120.981333 105.237333-208.64 217.6-197.397333 9.557333 0.96 16.96 1.834667 22.890667 2.816l-13.717334 84.224a313.429333 313.429333 0 0 0-17.706666-2.133334zM789.333333 661.333333c-31.616 0-64 29.504-64 74.666667s32.384 74.666667 64 74.666667 64-29.504 64-74.666667-32.384-74.666667-64-74.666667z" p-id="4763"></path></svg>
				</span>
				<span class="popover-button-text">&nbsp;{lang json_editor_toolbar_header6}</span>
			</button>
		</div>
	</div>
	<div class="Popover-content Popover-content--bottom Popover-content-enter-done Popover-hidden"
	     id="popover-list">
		<div class="Menu">
			<button type="button"
			        class="Button Popover-Button--plain Popover-Button--style"
			        onclick="return convertBlock('list', { style: 'unordered' }, event)">
							<span style="display: inline-flex; align-items: center;">
								<svg t="1747386416670" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="9459" width="30" height="30"><path d="M315.076923 523.815385c0-28.278154 21.110154-51.2 47.182769-51.2h565.956923c26.072615 0 47.261538 22.921846 47.261539 51.2 0 28.278154-21.188923 51.2-47.261539 51.2H362.338462C336.265846 575.015385 315.076923 552.093538 315.076923 523.815385m0 393.846153c0-28.278154 21.110154-51.2 47.182769-51.2h565.956923c26.072615 0 47.261538 22.921846 47.261539 51.2 0 28.278154-21.188923 51.2-47.261539 51.2H362.338462c-25.993846 0-47.182769-22.921846-47.18277-51.2m0-787.692307c0-28.278154 21.110154-51.2 47.18277-51.2h565.956923c26.072615 0 47.261538 22.921846 47.261538 51.2 0 28.278154-21.188923 51.2-47.261538 51.2H362.338462C336.265846 181.169231 315.076923 158.247385 315.076923 129.969231M157.538462 535.630769a78.769231 78.769231 0 1 1-157.538462 0 78.769231 78.769231 0 0 1 157.538462 0m0-417.476923a78.769231 78.769231 0 1 1-157.538462 0 78.769231 78.769231 0 0 1 157.538462 0m0 795.569231a78.769231 78.769231 0 1 1-157.538462 0 78.769231 78.769231 0 0 1 157.538462 0" fill="#333333" p-id="9460"></path></svg>
							</span>
				<span class="popover-button-text">&nbsp;{lang json_editor_toolbar_list_unordered}</span>
			</button>
			<button type="button"
			        class="Button Popover-Button--plain Popover-Button--style"
			        onclick="return convertBlock('list', { style: 'ordered' }, event)">
							<span style="display: inline-flex; align-items: center;">
								<svg t="1746791360059" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="25582" width="30" height="30"><path d="M256 100.9664h768v76.8H256zM256 484.9664h768v76.8H256zM256 868.9664h768v76.8H256zM19.0464 97.6896v-28.7232c9.3696 0.7168 18.7392 0.7168 28.1088 0a39.4752 39.4752 0 0 0 20.7872-11.6224 37.0176 37.0176 0 0 0 8.5504-15.2576 34.1504 34.1504 0 0 0 0-8.5504h39.0656v213.1456h-45.824v-148.992h-50.688z m0 473.344c10.0352-18.7392 24.7808-34.5088 42.752-45.824 13.3632-9.0112 26.0096-18.9952 37.888-29.952a44.3904 44.3904 0 0 0 13.4144-31.1296 33.9456 33.9456 0 0 0-7.3216-22.5792 26.9312 26.9312 0 0 0-22.016-9.1648 26.2656 26.2656 0 0 0-29.2864 14.0288 71.168 71.168 0 0 0-4.9152 26.2656H9.8816a104.0896 104.0896 0 0 1 9.7792-44.5952 61.1328 61.1328 0 0 1 61.0816-32.3584c19.9168-1.024 39.424 5.7856 54.3744 18.944 13.5168 13.056 20.8384 31.2832 20.1216 50.0736 0.256 15.4112-4.7104 30.5152-14.08 42.752a144.7424 144.7424 0 0 1-31.1296 27.4944l-17.1008 12.2368-21.9648 16.4864a48.384 48.384 0 0 0-9.7792 11.6224h94.0544v37.2736H7.424c0.2048-14.592 3.5328-28.9792 9.7792-42.1376l1.8432 0.5632z m30.5152 342.6304a48.9984 48.9984 0 0 0 4.3008 21.4016c5.2224 10.5472 16.384 16.8448 28.1088 15.872a31.488 31.488 0 0 0 19.5584-6.0928c6.4-6.4512 9.728-15.36 9.1648-24.4224a28.16 28.16 0 0 0-17.1008-28.7232 87.9104 87.9104 0 0 0-30.5152-7.936v-30.5152c9.728 0.3072 19.456-1.1264 28.7232-4.3008a24.9856 24.9856 0 0 0 14.0288-26.2656 28.16 28.16 0 0 0-7.3216-20.1728 26.3168 26.3168 0 0 0-20.1728-7.936 25.5488 25.5488 0 0 0-21.9648 9.7792 39.7312 39.7312 0 0 0-6.8096 25.6512H9.8816c0.3584-10.3936 2.2016-20.6848 5.4784-30.5152 3.84-8.8064 9.4208-16.6912 16.4864-23.1936 5.6832-5.12 12.3392-9.0624 19.5584-11.6224 9.1136-2.56 18.6368-3.584 28.1088-3.072 18.0736-0.9216 35.84 4.7104 50.0736 15.872 12.5952 10.5472 19.5584 26.3168 18.944 42.752a47.4112 47.4112 0 0 1-11.6224 32.3584 38.7584 38.7584 0 0 1-14.6432 11.008 30.2592 30.2592 0 0 1 16.4864 9.7792c11.1616 10.5984 17.2032 25.5488 16.4864 40.9088 0.2048 17.92-6.6048 35.2256-18.944 48.2304-14.8992 14.6944-35.328 22.2208-56.1664 20.7872a68.4032 68.4032 0 0 1-61.0816-29.952 89.6 89.6 0 0 1-10.3936-41.5232l40.9088 1.8432z" fill="#181818" p-id="25583"></path></svg>
							</span>
				<span class="popover-button-text">&nbsp;{lang json_editor_toolbar_list_ordered}</span>
			</button>
		</div>
	</div>
	<!--{hook/post_jsoneditor_toolbar_popover}-->
</div>